Imports System.IO
Imports iTextSharp.text
Imports iTextSharp.text.pdf
Imports System.Data.SqlClient

Public Class FormAdmin
    Dim conn As New SqlConnection("Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True")
    Dim da As SqlDataAdapter
    Dim ds As DataSet
    Dim cmd As SqlCommand

    Sub LoadEventPengajuan()
        Dim query As String = "SELECT * FROM event_pengajuan WHERE event_id NOT IN (SELECT event_id FROM verifikasi_event)"
        da = New SqlDataAdapter(query, conn)
        ds = New DataSet()
        da.Fill(ds, "event_pengajuan")
        dgv_event_pengajuan.DataSource = ds.Tables("event_pengajuan")

        With dgv_event_pengajuan
            If .Columns.Contains("event_id") Then .Columns("event_id").HeaderText = "Event ID"
            If .Columns.Contains("user_id") Then .Columns("user_id").HeaderText = "User ID"
            If .Columns.Contains("judul_event") Then .Columns("judul_event").HeaderText = "Judul Event"
            If .Columns.Contains("jenis_kegiatan") Then .Columns("jenis_kegiatan").HeaderText = "Jenis Kegiatan"
            If .Columns.Contains("Total_pembiayaan") Then .Columns("Total_pembiayaan").HeaderText = "Total Pembiayaan"
            If .Columns.Contains("Proposal") Then .Columns("Proposal").HeaderText = "Proposal"
            If .Columns.Contains("deskripsi") Then .Columns("deskripsi").HeaderText = "Deskripsi"
            If .Columns.Contains("tanggal_pengajuan") Then .Columns("tanggal_pengajuan").HeaderText = "Tanggal Pengajuan"
            If .Columns.Contains("status") Then .Columns("status").HeaderText = "Status"
        End With
    End Sub

    Sub LoadVerifikasiEvent()
        Dim query As String = "SELECT * FROM verifikasi_event"
        da = New SqlDataAdapter(query, conn)
        ds = New DataSet()
        da.Fill(ds, "verifikasi_event")
        dgv_verifikasi_event.DataSource = ds.Tables("verifikasi_event")

        With dgv_verifikasi_event
            If .Columns.Contains("verifikasi_id") Then .Columns("verifikasi_id").HeaderText = "Verifikasi ID"
            If .Columns.Contains("event_id") Then .Columns("event_id").HeaderText = "Event ID"
            If .Columns.Contains("admin_id") Then .Columns("admin_id").HeaderText = "Admin ID"
            If .Columns.Contains("tanggal_verifikasi") Then .Columns("tanggal_verifikasi").HeaderText = "Tanggal Verifikasi"
            If .Columns.Contains("catatan_admin") Then .Columns("catatan_admin").HeaderText = "Catatan Admin"
            If .Columns.Contains("Status") Then .Columns("Status").HeaderText = "Status"
        End With
    End Sub

    Function GetNextVerifikasiId() As Integer
        Dim id As Integer = 1
        conn.Open()
        cmd = New SqlCommand("SELECT ISNULL(MAX(verifikasi_id),0)+1 FROM verifikasi_event", conn)
        id = cmd.ExecuteScalar()
        conn.Close()
        Return id
    End Function

    Private Sub FormAdmin_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txt_verifikasiid.Text = GetNextVerifikasiId().ToString()
        LoadEventPengajuan()
        LoadVerifikasiEvent()

    End Sub

    Private Sub dgv_event_pengajuan_CellClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgv_event_pengajuan.CellClick
        If e.RowIndex >= 0 Then
            Dim row As DataGridViewRow = dgv_event_pengajuan.Rows(e.RowIndex)
            txt_eventid.Text = row.Cells("event_id").Value.ToString()
        End If
    End Sub

    Private Sub btn_setuju_Click(sender As Object, e As EventArgs) Handles btn_setuju.Click
        SimpanVerifikasi("Disetujui")
    End Sub

    Private Sub btn_tolak_Click(sender As Object, e As EventArgs) Handles btn_tolak.Click
        SimpanVerifikasi("Ditolak")
    End Sub

    Sub SimpanVerifikasi(status As String)
        If txt_eventid.Text = "" Or txt_adminid.Text = "" Then
            MessageBox.Show("Pilih event dan pastikan admin id terisi!")
            Return
        End If
        conn.Open()
        cmd = New SqlCommand("INSERT INTO verifikasi_event (event_id, admin_id, tanggal_verifikasi, catatan_admin, Status) VALUES (@event_id, @admin_id, @tanggal_verifikasi, @catatan_admin, @Status)", conn)
        cmd.Parameters.AddWithValue("@event_id", txt_eventid.Text)
        cmd.Parameters.AddWithValue("@admin_id", txt_adminid.Text)
        cmd.Parameters.AddWithValue("@tanggal_verifikasi", dtp_tglverifikasi.Value)
        cmd.Parameters.AddWithValue("@catatan_admin", txt_catatanadmin.Text)
        cmd.Parameters.AddWithValue("@Status", status)
        cmd.ExecuteNonQuery()
        conn.Close()
        MessageBox.Show("Verifikasi berhasil disimpan!")
        LoadEventPengajuan()
        LoadVerifikasiEvent()
    End Sub

    Private Sub Button1_Click(sender As Object, e As EventArgs)
        Dim result As DialogResult = MessageBox.Show("Apakah Anda yakin ingin logout?", "Konfirmasi Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
        If result = DialogResult.Yes Then
            FormLogin.Show()
            FormLogin.txt_username.Text = ""
            FormLogin.txt_password.Text = ""
            Me.Hide()
        End If
    End Sub

    Private Sub btn_pdf_Click(sender As Object, e As EventArgs) Handles btn_pdf.Click
        If dgv_verifikasi_event.Rows.Count = 0 Then
            MessageBox.Show("Tidak ada data untuk diekspor.", "Export PDF", MessageBoxButtons.OK, MessageBoxIcon.Information)
            Return
        End If

        Dim saveFileDialog As New SaveFileDialog()
        saveFileDialog.Filter = "PDF files (*.pdf)|*.pdf"
        saveFileDialog.FileName = "VerifikasiEvent_" & DateTime.Now.ToString("yyyyMMdd_HHmmss") & ".pdf"

        If saveFileDialog.ShowDialog() = DialogResult.OK Then
            Try
                Dim pdfDoc As New Document(PageSize.A4, 40, 40, 60, 50)
                Dim writer As PdfWriter = PdfWriter.GetInstance(pdfDoc, New FileStream(saveFileDialog.FileName, FileMode.Create))

                Dim pageEventHandler As New MyPdfPageEventHelper()
                writer.PageEvent = pageEventHandler

                pdfDoc.Open()

                Dim baseFont As BaseFont = BaseFont.CreateFont("C:\Windows\Fonts\times.ttf", BaseFont.IDENTITY_H, BaseFont.EMBEDDED)
                Dim fontHeader As New Font(baseFont, 14, Font.Bold)
                Dim fontCell As New Font(baseFont, 12, iTextSharp.text.Font.NORMAL)

                Dim title As New Paragraph("LAPORAN EVENT YANG DISETUJUI", fontHeader)
                title.Alignment = Element.ALIGN_CENTER
                title.SpacingAfter = 20
                pdfDoc.Add(title)

                Dim pdfTable As New PdfPTable(dgv_verifikasi_event.Columns.Count)
                pdfTable.WidthPercentage = 100
                pdfTable.SpacingBefore = 10
                pdfTable.SpacingAfter = 10

                For Each column As DataGridViewColumn In dgv_verifikasi_event.Columns
                    Dim cell As New PdfPCell(New Phrase(column.HeaderText, fontCell))
                    cell.BackgroundColor = New BaseColor(230, 230, 250)
                    cell.HorizontalAlignment = Element.ALIGN_CENTER
                    cell.Padding = 5
                    pdfTable.AddCell(cell)
                Next

                For Each row As DataGridViewRow In dgv_verifikasi_event.Rows
                    If Not row.IsNewRow Then
                        For Each cell As DataGridViewCell In row.Cells
                            pdfTable.AddCell(New Phrase(If(cell.Value IsNot Nothing, cell.Value.ToString(), ""), fontCell))
                        Next
                    End If
                Next

                pdfDoc.Add(pdfTable)
                pdfDoc.Close()

                MessageBox.Show("PDF berhasil disimpan.", "Sukses", MessageBoxButtons.OK, MessageBoxIcon.Information)

            Catch ex As Exception
                MessageBox.Show("Gagal mengekspor: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End If
    End Sub
    Public Class MyPdfPageEventHelper
        Inherits PdfPageEventHelper

        Private baseFont As BaseFont = BaseFont.CreateFont("C:\Windows\Fonts\times.ttf", BaseFont.IDENTITY_H, BaseFont.EMBEDDED)
        Private fontFooter As New Font(baseFont, 8, iTextSharp.text.Font.ITALIC)

        Public Overrides Sub OnEndPage(writer As PdfWriter, document As Document)
            Dim cb As PdfContentByte = writer.DirectContent
            Dim pageN As Integer = writer.PageNumber
            Dim text As String = "Halaman " & pageN.ToString()
            Dim dateText As String = "Dicetak: " & DateTime.Now.ToString("dd/MM/yyyy HH:mm")

            Dim len As Single = baseFont.GetWidthPoint(text, 8)

            cb.BeginText()
            cb.SetFontAndSize(baseFont, 8)
            cb.SetTextMatrix(document.LeftMargin, document.BottomMargin - 10)
            cb.ShowText(dateText)
            cb.EndText()

            cb.BeginText()
            cb.SetFontAndSize(baseFont, 8)
            cb.SetTextMatrix(document.PageSize.Width - document.RightMargin - len, document.BottomMargin - 10)
            cb.ShowText(text)
            cb.EndText()
        End Sub
    End Class

    Private Sub btn_crud_Click(sender As Object, e As EventArgs) Handles btn_crud.Click
        CRUDUser.Show()
        Me.Hide()
    End Sub

    Private Sub Button1_Click_1(sender As Object, e As EventArgs) Handles Button1.Click
        Dim result As DialogResult = MessageBox.Show("Apakah Anda yakin ingin logout?", "Konfirmasi Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
        If result = DialogResult.Yes Then
            FormLogin.Show()
            FormLogin.txt_id.Text = ""
            FormLogin.txt_username.Text = ""
            FormLogin.txt_password.Text = ""
            Me.Hide()
        End If
    End Sub
End Class