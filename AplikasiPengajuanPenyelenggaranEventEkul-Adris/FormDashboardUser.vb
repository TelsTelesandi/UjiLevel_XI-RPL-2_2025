Imports MySql.Data.MySqlClient

Public Class FormDashboardUser
    Public user_id As Integer

    Private Sub FormDashboardUser_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        TampilkanRekapEvent()
    End Sub

    ' --- TAMPILKAN DATA ---
    Private Sub TampilkanRekapEvent()
        Try
            Koneksi()
            Dim query As String = "SELECT event_id, judul_event, jenis_kegiatan, total_pembiayaan, status, tanggal_pengajuan 
                                   FROM event_pengajuan 
                                   WHERE user_id = @user_id 
                                   ORDER BY tanggal_pengajuan DESC"

            Using adapter As New MySqlDataAdapter(query, conn)
                adapter.SelectCommand.Parameters.AddWithValue("@user_id", user_id)
                Dim dt As New DataTable()
                adapter.Fill(dt)
                dgvRekapEvent.DataSource = dt
            End Using

            If conn.State = ConnectionState.Open Then conn.Close()
            FormatDataGridView()

        Catch ex As Exception
            MsgBox("Gagal menampilkan rekap event: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            If conn.State = ConnectionState.Open Then conn.Close()
        End Try
    End Sub

    ' --- FORMAT TABEL ---
    Private Sub FormatDataGridView()
        With dgvRekapEvent
            .AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill
            .AutoSizeRowsMode = DataGridViewAutoSizeRowsMode.AllCells
            .ScrollBars = ScrollBars.Both
            .DefaultCellStyle.WrapMode = DataGridViewTriState.True
            .AllowUserToAddRows = False
            .ReadOnly = True
            .SelectionMode = DataGridViewSelectionMode.FullRowSelect

            If .Columns.Contains("total_pembiayaan") Then
                .Columns("total_pembiayaan").DefaultCellStyle.Format = "C0"
                .Columns("total_pembiayaan").HeaderText = "Total Pembiayaan"
            End If
            If .Columns.Contains("judul_event") Then .Columns("judul_event").HeaderText = "Judul Event"
            If .Columns.Contains("jenis_kegiatan") Then .Columns("jenis_kegiatan").HeaderText = "Jenis Kegiatan"
            If .Columns.Contains("status") Then .Columns("status").HeaderText = "Status"
            If .Columns.Contains("tanggal_pengajuan") Then
                .Columns("tanggal_pengajuan").DefaultCellStyle.Format = "dd/MM/yyyy"
                .Columns("tanggal_pengajuan").HeaderText = "Tanggal Pengajuan"
            End If
            If .Columns.Contains("event_id") Then .Columns("event_id").Visible = False

            ' Format status & warna baris
            For Each row As DataGridViewRow In dgvRekapEvent.Rows
                Dim status = row.Cells("status").Value.ToString().ToLower()
                Select Case status
                    Case "approved"
                        row.Cells("status").Value = "Disetujui (Closed Request)"
                        row.DefaultCellStyle.BackColor = Color.LightGreen
                        row.DefaultCellStyle.ForeColor = Color.Black
                    Case "rejected"
                        row.Cells("status").Value = "Ditolak (Closed Request)"
                        row.DefaultCellStyle.BackColor = Color.LightCoral
                        row.DefaultCellStyle.ForeColor = Color.Black
                    Case "request", "menunggu"
                        row.DefaultCellStyle.BackColor = Color.Khaki
                        row.DefaultCellStyle.ForeColor = Color.Black
                    Case Else
                        row.DefaultCellStyle.BackColor = Color.White
                        row.DefaultCellStyle.ForeColor = Color.Black
                End Select
            Next
        End With
    End Sub

    ' --- TOMBOL ---
    Private Sub btnLogout_Click(sender As Object, e As EventArgs) Handles btnLogout.Click
        If MessageBox.Show("Apakah Anda yakin ingin logout?", "Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question) = DialogResult.Yes Then
            Me.Close()
            Form1.Show()
            Form1.txtUsername.Clear()
            Form1.txtPassword.Clear()
        End If
    End Sub

    Private Sub btnAjukanEvent_Click(sender As Object, e As EventArgs) Handles btnAjukanEvent.Click
        Dim f As New FormAjukanEvent()
        f.user_id = Me.user_id
        f.ShowDialog() ' tunggu ditutup
        TampilkanRekapEvent() ' refresh otomatis
    End Sub

    Private Sub btnGantipassword_Click(sender As Object, e As EventArgs) Handles btnGantipassword.Click
        Dim f As New FormGantiPassword()
        f.user_id = Me.user_id
        f.ShowDialog()
    End Sub

    Private Sub DataGridView1_CellContentClick(sender As Object, e As DataGridViewCellEventArgs)

    End Sub

    Private Sub dgvRekapEvent_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvRekapEvent.CellContentClick

    End Sub
End Class
