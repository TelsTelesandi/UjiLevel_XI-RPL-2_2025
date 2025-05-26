Imports System.Data.SqlClient

Public Class FormUser

    Private connectionString As String = "Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True"
    Private Sub FormUser_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        cb_filterstatus.Items.Clear()
        cb_filterstatus.Items.Add("Semua")
        cb_filterstatus.Items.Add("Menunggu")
        cb_filterstatus.Items.Add("Disetujui")
        cb_filterstatus.Items.Add("Ditolak")
        cb_filterstatus.SelectedIndex = 0
        TampilkanData()
    End Sub

    Private Sub TampilkanData(Optional filterStatus As String = "")
        Dim userId As Integer
        If Integer.TryParse(FormLogin.txt_id.Text, userId) = False Then
            MessageBox.Show("User ID tidak valid. Silakan login ulang!", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            Exit Sub
        End If
        Dim dt As New DataTable()
        Using conn As New SqlConnection(connectionString)
            Dim query As String = "SELECT event_id, judul_event, jenis_kegiatan, Total_pembiayaan, Proposal, deskripsi, tanggal_pengajuan, status FROM event_pengajuan WHERE user_id=@user_id"
            If filterStatus <> "" AndAlso filterStatus <> "Semua" Then
                query &= " AND status=@status"
            End If
            Using cmd As New SqlCommand(query, conn)
                cmd.Parameters.AddWithValue("@user_id", userId)
                If filterStatus <> "" AndAlso filterStatus <> "Semua" Then
                    cmd.Parameters.AddWithValue("@status", filterStatus)
                End If
                Dim da As New SqlDataAdapter(cmd)
                da.Fill(dt)
            End Using
        End Using
        dgv_dashboard.DataSource = dt
        dgv_dashboard.ClearSelection()

        If dgv_dashboard.Columns.Count > 0 Then
            If dgv_dashboard.Columns.Contains("event_id") Then
                dgv_dashboard.Columns("event_id").Visible = False
            End If

            dgv_dashboard.Columns("judul_event").HeaderText = "Judul Event"
            dgv_dashboard.Columns("jenis_kegiatan").HeaderText = "Jenis Kegiatan"
            dgv_dashboard.Columns("Total_pembiayaan").HeaderText = "Total Pembiayaan"
            dgv_dashboard.Columns("Proposal").HeaderText = "Proposal"
            dgv_dashboard.Columns("deskripsi").HeaderText = "Deskripsi"
            dgv_dashboard.Columns("tanggal_pengajuan").HeaderText = "Tanggal Pengajuan"
            dgv_dashboard.Columns("status").HeaderText = "Status"

            dgv_dashboard.Columns("judul_event").Width = 120
            dgv_dashboard.Columns("jenis_kegiatan").Width = 120
            dgv_dashboard.Columns("Total_pembiayaan").Width = 110
            dgv_dashboard.Columns("Proposal").Width = 100
            dgv_dashboard.Columns("deskripsi").Width = 150
            dgv_dashboard.Columns("tanggal_pengajuan").Width = 110
            dgv_dashboard.Columns("status").Width = 90

            dgv_dashboard.Columns("Total_pembiayaan").DefaultCellStyle.Alignment = DataGridViewContentAlignment.MiddleRight
            dgv_dashboard.Columns("tanggal_pengajuan").DefaultCellStyle.Alignment = DataGridViewContentAlignment.MiddleCenter
            dgv_dashboard.Columns("status").DefaultCellStyle.Alignment = DataGridViewContentAlignment.MiddleCenter

            dgv_dashboard.Columns("tanggal_pengajuan").DefaultCellStyle.Format = "dd-MM-yyyy"
        End If
    End Sub

    Private Sub cb_filterstatus_SelectedIndexChanged(sender As Object, e As EventArgs) Handles cb_filterstatus.SelectedIndexChanged
        Dim status As String = cb_filterstatus.SelectedItem.ToString()
        TampilkanData(status)
    End Sub

    Private Sub btn_kirim_Click(sender As Object, e As EventArgs) Handles btn_kirim.Click
        If txt_judulevent.Text = "" Or txt_jeniskegiatan.Text = "" Or txt_totalpembiayaan.Text = "" Or date_tglpengajuan.Text = "" Then
            MessageBox.Show("Semua field wajib diisi!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Exit Sub
        End If

        Dim userId As Integer
        If Integer.TryParse(FormLogin.txt_id.Text, userId) = False Then
            MessageBox.Show("User ID tidak valid. Silakan login ulang!", "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            Exit Sub
        End If

        Dim judulEvent As String = txt_judulevent.Text
        Dim jenisKegiatan As String = txt_jeniskegiatan.Text
        Dim totalPembiayaan As String = txt_totalpembiayaan.Text
        Dim proposal As String = txt_proposal.Text
        Dim deskripsi As String = txt_deskripsi.Text
        Dim tanggalPengajuan As Date = date_tglpengajuan.Value.Date
        Dim status As String = "Menunggu"

        Using conn As New SqlConnection(connectionString)
            Try
                conn.Open()
                Dim query As String = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, Total_pembiayaan, Proposal, deskripsi, tanggal_pengajuan, status) " &
                    "VALUES (@user_id, @judul_event, @jenis_kegiatan, @Total_pembiayaan, @Proposal, @deskripsi, @tanggal_pengajuan, @status)"
                Using cmd As New SqlCommand(query, conn)
                    cmd.Parameters.AddWithValue("@user_id", userId)
                    cmd.Parameters.AddWithValue("@judul_event", judulEvent)
                    cmd.Parameters.AddWithValue("@jenis_kegiatan", jenisKegiatan)
                    cmd.Parameters.AddWithValue("@Total_pembiayaan", totalPembiayaan)
                    cmd.Parameters.AddWithValue("@Proposal", If(String.IsNullOrEmpty(proposal), DBNull.Value, proposal))
                    cmd.Parameters.AddWithValue("@deskripsi", If(String.IsNullOrEmpty(deskripsi), DBNull.Value, deskripsi))
                    cmd.Parameters.AddWithValue("@tanggal_pengajuan", tanggalPengajuan)
                    cmd.Parameters.AddWithValue("@status", status)
                    cmd.ExecuteNonQuery()
                End Using
                MessageBox.Show("Pengajuan berhasil dikirim!", "Sukses", MessageBoxButtons.OK, MessageBoxIcon.Information)

                txt_judulevent.Clear()
                txt_jeniskegiatan.Clear()
                txt_totalpembiayaan.Clear()
                txt_proposal.Clear()
                txt_deskripsi.Clear()
                date_tglpengajuan.Value = Date.Now
                TampilkanData(cb_filterstatus.SelectedItem.ToString())
            Catch ex As Exception
                MessageBox.Show("Terjadi kesalahan: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
            End Try
        End Using
    End Sub

    Private Sub btnBatalkan_Click(sender As Object, e As EventArgs) Handles btn_batalkan.Click
        If dgv_dashboard.SelectedRows.Count = 0 Then
            MessageBox.Show("Pilih data pengajuan yang ingin dibatalkan!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Exit Sub
        End If
        Dim eventId As Integer = Convert.ToInt32(dgv_dashboard.SelectedRows(0).Cells("event_id").Value)
        Dim result As DialogResult = MessageBox.Show("Yakin ingin membatalkan pengajuan ini?", "Konfirmasi", MessageBoxButtons.YesNo, MessageBoxIcon.Question)
        If result = DialogResult.Yes Then
            Using conn As New SqlConnection(connectionString)
                Try
                    conn.Open()
                    Dim query As String = "DELETE FROM event_pengajuan WHERE event_id=@event_id"
                    Using cmd As New SqlCommand(query, conn)
                        cmd.Parameters.AddWithValue("@event_id", eventId)
                        cmd.ExecuteNonQuery()
                    End Using
                    MessageBox.Show("Pengajuan berhasil dibatalkan!", "Sukses", MessageBoxButtons.OK, MessageBoxIcon.Information)
                    TampilkanData(cb_filterstatus.SelectedItem.ToString())
                Catch ex As Exception
                    MessageBox.Show("Terjadi kesalahan: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
                End Try
            End Using
        End If
    End Sub

    Private Sub btn_logout_Click(sender As Object, e As EventArgs) Handles btn_logout.Click
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