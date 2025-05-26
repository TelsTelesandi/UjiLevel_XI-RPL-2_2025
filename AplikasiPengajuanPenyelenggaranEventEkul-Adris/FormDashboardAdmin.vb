Imports MySql.Data.MySqlClient

Public Class FormDashboardAdmin
    Public user_id As Integer

    Private Sub FormDashboardAdmin_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        LoadEventPengajuan()
    End Sub

    ' --- TAMPILKAN DATA ---
    Public Sub LoadEventPengajuan()
        Try
            Koneksi()
            Dim query As String = "SELECT event_id, judul_event, jenis_kegiatan, total_pembiayaan, deskripsi, proposal, status, tanggal_pengajuan FROM event_pengajuan ORDER BY tanggal_pengajuan DESC"
            Dim adapter As New MySqlDataAdapter(query, conn)
            Dim dt As New DataTable()
            adapter.Fill(dt)
            dgvEventAdmin.DataSource = dt
            FormatTabelEvent()
        Catch ex As Exception
            MsgBox("Gagal load data event: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    ' --- FORMAT TAMPILAN TABEL ---
    Private Sub FormatTabelEvent()
        dgvEventAdmin.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.None

        With dgvEventAdmin
            .ReadOnly = True
            .SelectionMode = DataGridViewSelectionMode.FullRowSelect
            .AllowUserToAddRows = False
            .DefaultCellStyle.WrapMode = DataGridViewTriState.True
        End With

        If Not dgvEventAdmin.Columns.Contains("status") Then Exit Sub

        For Each row As DataGridViewRow In dgvEventAdmin.Rows
            If row.Cells("status").Value Is Nothing Then Continue For

            ' ✅ Simpan status asli terlebih dahulu
            Dim originalStatus = row.Cells("status").Value.ToString().ToLower()
            row.Tag = originalStatus

            ' ✅ Hanya ubah tampilan
            Select Case originalStatus
                Case "approved"
                    row.Cells("status").Value = "Disetujui (Closed Request)"
                    row.DefaultCellStyle.BackColor = Color.LightGreen

                Case "rejected"
                    row.Cells("status").Value = "Ditolak (Closed Request)"
                    row.DefaultCellStyle.BackColor = Color.LightCoral

                Case "request", "pending"
                    row.DefaultCellStyle.BackColor = Color.Khaki

                Case Else
                    row.DefaultCellStyle.BackColor = Color.White
            End Select
        Next

        dgvEventAdmin.AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill
    End Sub



    ' --- UPDATE STATUS ---
    Private Sub UbahStatusEvent(statusBaru As String)
        If dgvEventAdmin.SelectedRows.Count = 0 Then
            MsgBox("Pilih salah satu pengajuan event.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Dim selectedRow = dgvEventAdmin.SelectedRows(0)
        Dim idEvent = selectedRow.Cells("event_id").Value
        Dim statusSekarang = selectedRow.Tag.ToString().ToLower()

        If statusSekarang = "approved" OrElse statusSekarang = "rejected" Then
            MsgBox("Event ini sudah disetujui atau ditolak.", MsgBoxStyle.Information)
            Exit Sub
        End If

        Try
            Koneksi()
            Dim cmd As New MySqlCommand("UPDATE event_pengajuan SET status = @status WHERE event_id = @id", conn)
            cmd.Parameters.AddWithValue("@status", statusBaru)
            cmd.Parameters.AddWithValue("@id", idEvent)
            cmd.ExecuteNonQuery()

            MsgBox("Status berhasil diperbarui menjadi: " & statusBaru, MsgBoxStyle.Information)
            LoadEventPengajuan()
        Catch ex As Exception
            MsgBox("Gagal ubah status: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    ' --- APPROVE / REJECT BUTTONS ---
    Private Sub btnApprove_Click(sender As Object, e As EventArgs) Handles btnApprove.Click
        UbahStatusEvent("approved")
    End Sub

    Private Sub btnReject_Click(sender As Object, e As EventArgs) Handles btnReject.Click
        UbahStatusEvent("rejected")
    End Sub

    ' --- SELECTION UNTUK ENABzLE BUTTONS ---
    Private Sub dgvEventAdmin_SelectionChanged(sender As Object, e As EventArgs) Handles dgvEventAdmin.SelectionChanged
        If dgvEventAdmin.SelectedRows.Count > 0 Then
            Dim selectedRow = dgvEventAdmin.SelectedRows(0)
            If selectedRow.Tag IsNot Nothing Then
                Dim status = selectedRow.Tag.ToString().ToLower()
                btnApprove.Enabled = (status = "request" Or status = "pending")
                btnReject.Enabled = (status = "request" Or status = "pending")
                btnDelete.Enabled = True
            Else
                btnApprove.Enabled = False
                btnReject.Enabled = False
                btnDelete.Enabled = False
            End If
        Else
            btnApprove.Enabled = False
            btnReject.Enabled = False
            btnDelete.Enabled = False
        End If
    End Sub


    ' --- Akses Lain ---
    Private Sub btnHakAkses_Click(sender As Object, e As EventArgs) Handles btnHakAkses.Click
        FormKelolaUser.Show()
        Me.Hide()
    End Sub

    Private Sub btnLaporan_Click(sender As Object, e As EventArgs) Handles btnLaporan.Click
        FormLaporan.Show()
        Me.Close()
    End Sub

    Private Sub btnGantiPassword_Click(sender As Object, e As EventArgs) Handles btnGantiPassword.Click
        Dim f As New FormGantiPassword()
        f.user_id = Me.user_id
        f.ShowDialog()
    End Sub

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        If MessageBox.Show("Apakah Anda yakin ingin logout?", "Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question) = DialogResult.Yes Then
            Me.Hide()
            Form1.Show()
            Form1.txtUsername.Clear()
            Form1.txtPassword.Clear()
        End If
    End Sub

    Private Sub btnDelete_Click(sender As Object, e As EventArgs) Handles btnDelete.Click

    End Sub

    Private Sub btnDashboard_Click(sender As Object, e As EventArgs) Handles btnDashboard.Click

    End Sub
End Class
