Imports System.Drawing
Imports MySql.Data.MySqlClient
Imports System.IO
Imports iTextSharp.text
Imports iTextSharp.text.pdf
Imports System.Drawing.Printing



Public Class FormLaporan
    Private printDoc As New PrintDocument
    Private printPageIndex As Integer

    Private Sub btnCetak_Click(sender As Object, e As EventArgs) Handles btnCetak.Click
        If dgvLaporan.Rows.Count = 0 Then
            MsgBox("Tidak ada data untuk dicetak.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        AddHandler printDoc.PrintPage, AddressOf PrintDoc_PrintPage
        printPageIndex = 0
        PrintPreviewDialog1.Document = printDoc
        PrintPreviewDialog1.ShowDialog()
    End Sub


    Private Sub PrintDoc_PrintPage(sender As Object, e As PrintPageEventArgs)
        Dim font As New System.Drawing.Font("Arial", 10, FontStyle.Regular)
        Dim y As Integer = 100
        Dim rowHeight As Integer = 25
        Dim x As Integer = 50

        ' Header
        For Each col As DataGridViewColumn In dgvLaporan.Columns
            e.Graphics.DrawString(col.HeaderText, font, Brushes.Black, x, y)
            x += 150
        Next

        y += rowHeight
        x = 50

        ' Data
        For i = printPageIndex To dgvLaporan.Rows.Count - 1
            For Each cell As DataGridViewCell In dgvLaporan.Rows(i).Cells
                e.Graphics.DrawString(If(cell.Value IsNot Nothing, cell.Value.ToString(), ""), font, Brushes.Black, x, y)
                x += 150
            Next
            y += rowHeight
            x = 50

            If y > e.MarginBounds.Height Then
                printPageIndex = i + 1
                e.HasMorePages = True
                Exit Sub
            End If
        Next

        e.HasMorePages = False
        RemoveHandler printDoc.PrintPage, AddressOf PrintDoc_PrintPage
    End Sub
    Private Sub FormLaporan_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        cmbKategori.Items.Add("Pengajuan Aktif")
        cmbKategori.Items.Add("Closed Request")
        cmbKategori.SelectedIndex = 0
    End Sub

    Private Sub LoadLaporan()
        Try
            Koneksi()
            Dim query As String = ""

            If cmbKategori.Text = "Pengajuan Aktif" Then
                query = "SELECT judul_event, jenis_kegiatan, total_pembiayaan, status, tanggal_pengajuan " &
                        "FROM event_pengajuan " &
                        "WHERE status IN ('request', 'pending') " &
                        "ORDER BY tanggal_pengajuan DESC"

            ElseIf cmbKategori.Text = "Closed Request" Then
                query = "SELECT judul_event, jenis_kegiatan, total_pembiayaan, status, tanggal_pengajuan " &
                        "FROM event_pengajuan " &
                        "WHERE status IN ('approved', 'rejected') " &
                        "ORDER BY tanggal_pengajuan DESC"
            End If

            Dim adapter As New MySqlDataAdapter(query, conn)
            Dim dt As New DataTable()
            adapter.Fill(dt)
            dgvLaporan.DataSource = dt

        Catch ex As Exception
            MsgBox("Gagal memuat laporan: " & ex.Message)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub btnTampilkan_Click(sender As Object, e As EventArgs) Handles btnTampilkan.Click
        LoadLaporan()
    End Sub


    Private Sub btnExportPDF_Click(sender As Object, e As EventArgs) Handles btnExportPDF.Click
        If dgvLaporan.Rows.Count = 0 Then
            MsgBox("Tidak ada data untuk diexport.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Try
            Dim pdfTable As New PdfPTable(dgvLaporan.ColumnCount)
            pdfTable.DefaultCell.Padding = 3
            pdfTable.WidthPercentage = 100
            pdfTable.HorizontalAlignment = Element.ALIGN_LEFT

            ' Header
            For Each column As DataGridViewColumn In dgvLaporan.Columns
                pdfTable.AddCell(New Phrase(column.HeaderText))
            Next

            ' Data
            For Each row As DataGridViewRow In dgvLaporan.Rows
                For Each cell As DataGridViewCell In row.Cells
                    pdfTable.AddCell(If(cell.Value IsNot Nothing, cell.Value.ToString(), ""))
                Next
            Next

            Dim saveFileDialog As New SaveFileDialog()
            saveFileDialog.FileName = "Laporan_" & cmbKategori.Text.Replace(" ", "_") & ".pdf"
            saveFileDialog.DefaultExt = ".pdf"
            If saveFileDialog.ShowDialog() = DialogResult.OK Then
                Using stream As New FileStream(saveFileDialog.FileName, FileMode.Create)
                    Dim pdfDoc As New Document(PageSize.A4, 10, 10, 10, 10)
                    PdfWriter.GetInstance(pdfDoc, stream)
                    pdfDoc.Open()
                    pdfDoc.Add(New Paragraph("Laporan " & cmbKategori.Text))
                    pdfDoc.Add(New Paragraph(" "))
                    pdfDoc.Add(pdfTable)
                    pdfDoc.Close()
                    stream.Close()
                End Using
                MsgBox("PDF berhasil disimpan!", MsgBoxStyle.Information)
            End If

        Catch ex As Exception
            MsgBox("Gagal export PDF: " & ex.Message, MsgBoxStyle.Critical)
        End Try
    End Sub


    Private Sub btnKembali_Click(sender As Object, e As EventArgs) Handles btnKembali.Click
        FormDashboardAdmin.Show()
        Me.Close()
    End Sub

    Private Sub PrintDocument1_PrintPage(sender As Object, e As PrintPageEventArgs) Handles PrintDocument1.PrintPage
        Dim fontRegular As New System.Drawing.Font("Arial", 10, System.Drawing.FontStyle.Regular)
        Dim fontHeader As New System.Drawing.Font("Arial", 12, System.Drawing.FontStyle.Bold)
        Dim fontTitle As New System.Drawing.Font("Arial", 14, System.Drawing.FontStyle.Bold)


        Dim y As Integer = 100
        Dim xStart As Integer = 50
        Dim x As Integer = xStart
        Dim rowHeight As Integer = 30
        Dim colWidths(dgvLaporan.Columns.Count - 1) As Integer

        ' Hitung lebar kolom
        Dim totalWidth As Integer = e.MarginBounds.Width
        Dim defaultColWidth As Integer = totalWidth \ dgvLaporan.Columns.Count
        For i = 0 To dgvLaporan.Columns.Count - 1
            colWidths(i) = defaultColWidth
        Next

        ' Judul laporan (tengah)
        Dim titleText As String = "LAPORAN " & cmbKategori.Text.ToUpper()
        Dim titleSize = e.Graphics.MeasureString(titleText, fontTitle)
        e.Graphics.DrawString(titleText, fontTitle, Brushes.Black, (e.PageBounds.Width - titleSize.Width) / 2, y)
        y += rowHeight

        ' Tanggal di kanan atas
        Dim tanggalText = "Tanggal: " & Now.ToString("dd MMMM yyyy")
        Dim tanggalSize = e.Graphics.MeasureString(tanggalText, fontRegular)
        e.Graphics.DrawString(tanggalText, fontRegular, Brushes.Black, e.MarginBounds.Right - tanggalSize.Width, y)
        y += rowHeight

        ' Header tabel
        x = xStart
        For i = 0 To dgvLaporan.Columns.Count - 1
            e.Graphics.FillRectangle(Brushes.LightGray, x, y, colWidths(i), rowHeight)
            e.Graphics.DrawRectangle(Pens.Black, x, y, colWidths(i), rowHeight)
            e.Graphics.DrawString(dgvLaporan.Columns(i).HeaderText, fontHeader, Brushes.Black, New RectangleF(x, y, colWidths(i), rowHeight))
            x += colWidths(i)
        Next
        y += rowHeight

        ' Isi data
        For i = printPageIndex To dgvLaporan.Rows.Count - 1
            x = xStart
            For j = 0 To dgvLaporan.Columns.Count - 1
                Dim text As String = If(dgvLaporan.Rows(i).Cells(j).Value IsNot Nothing, dgvLaporan.Rows(i).Cells(j).Value.ToString(), "")
                e.Graphics.DrawRectangle(Pens.Black, x, y, colWidths(j), rowHeight)
                e.Graphics.DrawString(text, fontRegular, Brushes.Black, New RectangleF(x, y, colWidths(j), rowHeight))
                x += colWidths(j)
            Next
            y += rowHeight

            ' Halaman selanjutnya
            If y + rowHeight > e.MarginBounds.Bottom Then
                printPageIndex = i + 1
                e.HasMorePages = True
                Return
            End If
        Next

        e.HasMorePages = False
        RemoveHandler printDoc.PrintPage, AddressOf PrintDoc_PrintPage
    End Sub
End Class
