Imports MySql.Data.MySqlClient

Public Class FormKelolaUser
    Public user_id As Integer



    Private Sub Label4_Click(sender As Object, e As EventArgs) Handles Label4.Click

    End Sub

    Private Sub FormKelolaUser_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        LoadHakAkses()
        LoadUsers()
    End Sub

    Private Sub LoadHakAkses()
        cmbRole.Items.Add("admin")
        cmbRole.Items.Add("user")
    End Sub

    Private Sub LoadUsers()
        Try
            Koneksi()
            Dim query As String = "SELECT user_id, username, nama_lengkap, role , Ekskul FROM users ORDER BY user_id"
            Dim da As New MySqlDataAdapter(query, conn)
            Dim dt As New DataTable()
            da.Fill(dt)
            dgvUsers.DataSource = dt
        Catch ex As Exception
            MsgBox("Gagal mengambil data users: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub btnTambah_Click(sender As Object, e As EventArgs) Handles btnTambah.Click
        If txtUsername.Text = "" Or txtPassword.Text = "" Or txtNama.Text = "" Or cmbRole.Text = "" Then
            MsgBox("Semua field harus diisi!", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Try
            Koneksi()
            Dim query As String = "INSERT INTO users (username, password, nama_lengkap, role) VALUES (@user, @pass, @nama, @role)"
            Dim cmd As New MySqlCommand(query, conn)
            cmd.Parameters.AddWithValue("@user", txtUsername.Text)
            cmd.Parameters.AddWithValue("@pass", txtPassword.Text)
            cmd.Parameters.AddWithValue("@nama", txtNama.Text)
            cmd.Parameters.AddWithValue("@role", cmbRole.Text)
            cmd.Parameters.AddWithValue("@Ekskul", txtEkskul.Text)
            cmd.ExecuteNonQuery()
            MsgBox("User berhasil ditambahkan!")
            LoadUsers()
            ClearForm()
        Catch ex As Exception
            MsgBox("Gagal menambah user: " & ex.Message)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub dgvUsers_CellClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgvUsers.CellClick
        If e.RowIndex >= 0 Then
            Dim row = dgvUsers.Rows(e.RowIndex)
            txtUsername.Text = row.Cells("username").Value
            txtNama.Text = row.Cells("nama_lengkap").Value
            txtEkskul.Text = row.Cells("Ekskul").Value
            cmbRole.Text = row.Cells("role").Value


        End If
    End Sub

    Private Sub btnUpdate_Click(sender As Object, e As EventArgs) Handles btnUpdate.Click
        If dgvUsers.SelectedRows.Count = 0 Then
            MsgBox("Pilih user yang ingin diperbarui.")
            Exit Sub
        End If
        Dim id As Integer = dgvUsers.SelectedRows(0).Cells("user_id").Value

        Try
            Koneksi()
            Dim query As String = "UPDATE users SET username=@user, nama_lengkap=@nama, role=@role WHERE user_id=@id"
            Dim cmd As New MySqlCommand(query, conn)
            cmd.Parameters.AddWithValue("@user", txtUsername.Text)
            cmd.Parameters.AddWithValue("@nama", txtNama.Text)
            cmd.Parameters.AddWithValue("@Ekskul", txtEkskul.Text)
            cmd.Parameters.AddWithValue("@role", cmbRole.Text)
            cmd.Parameters.AddWithValue("@id", id)
            cmd.ExecuteNonQuery()
            MsgBox("User diperbarui.")
            LoadUsers()
            ClearForm()
        Catch ex As Exception
            MsgBox("Gagal update user: " & ex.Message)
        Finally
            conn.Close()
        End Try
    End Sub


    Private Sub btnHapus_Click(sender As Object, e As EventArgs) Handles btnHapus.Click
        If dgvUsers.SelectedRows.Count = 0 Then
            MsgBox("Pilih user yang ingin dihapus.")
            Exit Sub
        End If
        Dim id As Integer = dgvUsers.SelectedRows(0).Cells("user_id").Value

        If MessageBox.Show("Yakin ingin menghapus user ini?", "Konfirmasi", MessageBoxButtons.YesNo) = DialogResult.Yes Then
            Try
                Koneksi()
                Dim query As String = "DELETE FROM users WHERE user_id=@id"
                Dim cmd As New MySqlCommand(query, conn)
                cmd.Parameters.AddWithValue("@id", id)
                cmd.ExecuteNonQuery()
                MsgBox("User dihapus.")
                LoadUsers()
                ClearForm()
            Catch ex As Exception
                MsgBox("Gagal hapus user: " & ex.Message)
            Finally
                conn.Close()
            End Try
        End If
    End Sub

    Private Sub ClearForm()
        txtUsername.Clear()
        txtPassword.Clear()
        txtNama.Clear()
        txtEkskul.Clear()
        cmbRole.SelectedIndex = -1
    End Sub


    Private Sub btnClear_Click(sender As Object, e As EventArgs) Handles btnClear.Click
        ClearForm()
    End Sub

    Private Sub btnKembali_Click(sender As Object, e As EventArgs)
        FormDashboardAdmin.Show()
        Me.Close()

    End Sub

    Private Sub btnDashboard_Click(sender As Object, e As EventArgs) Handles btnDashboard.Click
        FormDashboardAdmin.Show()
        Me.Hide()

    End Sub

    Private Sub btnGantiPassword_Click(sender As Object, e As EventArgs) Handles btnGantiPassword.Click
        Dim f As New FormGantiPassword()
        f.user_id = Me.user_id ' WAJIB! agar tahu siapa yang login
        f.ShowDialog()
    End Sub

    Private Sub btnLogout_Click(sender As Object, e As EventArgs) Handles btnLogout.Click
        If MessageBox.Show("Apakah Anda yakin ingin logout?", "Logout", MessageBoxButtons.YesNo, MessageBoxIcon.Question) = DialogResult.Yes Then
            Me.Hide()
            Form1.Show()
            Form1.txtUsername.Clear()
            Form1.txtPassword.Clear()
        End If
    End Sub

    Private Sub btnLaporan_Click(sender As Object, e As EventArgs) Handles btnLaporan.Click
        FormLaporan.Show()
        Me.Close()
    End Sub
End Class