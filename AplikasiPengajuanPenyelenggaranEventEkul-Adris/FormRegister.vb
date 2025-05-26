Imports MySql.Data.MySqlClient

Public Class FormRegister
    Private Sub FormRegister_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        ' Tidak perlu role, karena default 'user'
    End Sub

    Private Sub btnDaftar_Click(sender As Object, e As EventArgs) Handles btnDaftar.Click
        If txtUsername.Text = "" Or txtPassword.Text = "" Or txtKonfirmasi.Text = "" Or txtNama.Text = "" Or txtEkskul.Text = "" Then
            MsgBox("Semua field wajib diisi.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        If txtPassword.Text <> txtKonfirmasi.Text Then
            MsgBox("Password dan konfirmasi tidak sama.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Try
            Koneksi()

            ' Cek apakah username sudah digunakan
            Dim cmdCheck As New MySqlCommand("SELECT COUNT(*) FROM users WHERE username = @user", conn)
            cmdCheck.Parameters.AddWithValue("@user", txtUsername.Text)
            Dim count = Convert.ToInt32(cmdCheck.ExecuteScalar())

            If count > 0 Then
                MsgBox("Username sudah digunakan. Gunakan yang lain.", MsgBoxStyle.Exclamation)
                Exit Sub
            End If

            ' Simpan user baru (role: user)
            Dim cmdInsert As New MySqlCommand("INSERT INTO users (username, password, nama_lengkap, role, ekskul) VALUES (@user, @pass, @nama, 'user', @ekskul)", conn)
            cmdInsert.Parameters.AddWithValue("@user", txtUsername.Text)
            cmdInsert.Parameters.AddWithValue("@pass", txtPassword.Text)
            cmdInsert.Parameters.AddWithValue("@nama", txtNama.Text)
            cmdInsert.Parameters.AddWithValue("@ekskul", txtEkskul.Text)
            cmdInsert.ExecuteNonQuery()

            MsgBox("Pendaftaran berhasil! Silakan login.", MsgBoxStyle.Information)
            Form1.Show()
            Me.Close()

        Catch ex As Exception
            MsgBox("Gagal mendaftar: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub btnBatal_Click(sender As Object, e As EventArgs) Handles btnBatal.Click
        Form1.Show()
        Me.Close()
    End Sub
End Class
