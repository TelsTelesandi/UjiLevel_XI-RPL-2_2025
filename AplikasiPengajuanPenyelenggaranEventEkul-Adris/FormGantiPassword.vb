Imports MySql.Data.MySqlClient

Public Class FormGantiPassword
    Public user_id As Integer
    Public user_role As String

    Private Sub FormGantiPassword_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        Try
            Koneksi()
            Dim cmd As New MySqlCommand("SELECT username, nama_lengkap, ekskul FROM users WHERE user_id = @id", conn)
            cmd.Parameters.AddWithValue("@id", user_id)
            Dim reader = cmd.ExecuteReader()
            If reader.Read() Then
                txtusername.Text = reader("username").ToString()
                txtNamaLengkap.Text = reader("nama_lengkap").ToString()
                txtEkskul.Text = reader("ekskul").ToString()
            End If
            reader.Close()
        Catch ex As Exception
            MsgBox("Gagal memuat data pengguna: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub btnGantiPassword_Click(sender As Object, e As EventArgs) Handles btnGantiPassword.Click
        If txtPasswordLama.Text = "" Or txtPasswordBaru.Text = "" Or txtKonfirmasiPassword.Text = "" Or
           txtusername.Text = "" Or txtNamaLengkap.Text = "" Or txtEkskul.Text = "" Then
            MsgBox("Semua field wajib diisi.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        If txtPasswordBaru.Text <> txtKonfirmasiPassword.Text Then
            MsgBox("Password baru dan konfirmasi tidak cocok.", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Try
            Koneksi()

            ' Cek password lama
            Dim cekQuery As String = "SELECT * FROM users WHERE user_id = @id AND password = @old"
            Dim cekCmd As New MySqlCommand(cekQuery, conn)
            cekCmd.Parameters.AddWithValue("@id", user_id)
            cekCmd.Parameters.AddWithValue("@old", txtPasswordLama.Text)
            Dim reader = cekCmd.ExecuteReader()

            If reader.Read() Then
                reader.Close()

                ' Cek apakah username sudah dipakai oleh user lain
                Dim cekUsername = New MySqlCommand("SELECT COUNT(*) FROM users WHERE username = @user AND user_id <> @id", conn)
                cekUsername.Parameters.AddWithValue("@user", txtusername.Text)
                cekUsername.Parameters.AddWithValue("@id", user_id)
                Dim count = Convert.ToInt32(cekUsername.ExecuteScalar())
                If count > 0 Then
                    MsgBox("Username sudah dipakai, silakan gunakan yang lain.", MsgBoxStyle.Exclamation)
                    Exit Sub
                End If

                ' Update semua
                Dim updateQuery As String = "UPDATE users SET password = @newpass, username = @user, nama_lengkap = @nama, ekskul = @ekskul WHERE user_id = @id"
                Dim updateCmd As New MySqlCommand(updateQuery, conn)
                updateCmd.Parameters.AddWithValue("@newpass", txtPasswordBaru.Text)
                updateCmd.Parameters.AddWithValue("@user", txtusername.Text)
                updateCmd.Parameters.AddWithValue("@nama", txtNamaLengkap.Text)
                updateCmd.Parameters.AddWithValue("@ekskul", txtEkskul.Text)
                updateCmd.Parameters.AddWithValue("@id", user_id)
                updateCmd.ExecuteNonQuery()

                MsgBox("Data berhasil diperbarui.", MsgBoxStyle.Information)

                ' Redirect ke dashboard
                If user_role = "admin" Then
                    Dim admin As New FormDashboardAdmin()
                    admin.user_id = Me.user_id
                    admin.Show()
                Else
                    Dim user As New FormDashboardUser()
                    user.user_id = Me.user_id
                    user.Show()
                End If

                Me.Close()
            Else
                MsgBox("Password lama salah.", MsgBoxStyle.Critical)
            End If

        Catch ex As Exception
            MsgBox("Terjadi kesalahan: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub btnBatal_Click(sender As Object, e As EventArgs) Handles btnBatal.Click
        If user_role = "admin" Then
            FormDashboardAdmin.Show()
        Else
            FormDashboardUser.Show()
        End If
        Me.Close()
    End Sub
End Class
