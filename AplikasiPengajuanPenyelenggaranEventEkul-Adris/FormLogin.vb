Imports MySql.Data.MySqlClient

Public Class Form1
    Private Sub btnLogin_Click(sender As Object, e As EventArgs) Handles btnLogin.Click
        If txtUsername.Text.Trim() = "" Or txtPassword.Text.Trim() = "" Then
            MsgBox("Username dan Password tidak boleh kosong!", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Try
            Koneksi()
            Dim query As String = "SELECT * FROM users WHERE username=@user AND password=@pass"
            cmd = New MySqlCommand(query, conn)
            cmd.Parameters.AddWithValue("@user", txtUsername.Text)
            cmd.Parameters.AddWithValue("@pass", txtPassword.Text)

            dr = cmd.ExecuteReader
            If dr.Read() Then
                MsgBox("Login Berhasil sebagai " & dr("role"), MsgBoxStyle.Information)

                If dr("role") = "admin" Then
                    FormDashboardAdmin.lblnama.Text = dr("nama_lengkap")
                    FormDashboardAdmin.lblrole.Text = dr("role")
                    FormDashboardAdmin.user_id = dr("user_id")
                    FormDashboardAdmin.LoadEventPengajuan()

                    FormKelolaUser.lblnama.Text = dr("nama_lengkap")
                    FormKelolaUser.lblrole.Text = dr("role")
                    FormKelolaUser.user_id = dr("user_id")

                    FormDashboardAdmin.Show()
                Else
                    FormDashboardUser.lblnama.Text = dr("nama_lengkap")
                    FormDashboardUser.lblrole.Text = dr("role")
                    FormDashboardUser.user_id = dr("user_id")

                    FormDashboardUser.Show()
                End If

                ' ✅ HIDE, bukan CLOSE
                txtUsername.Clear()
                txtPassword.Clear()
                Me.Hide()

            Else
                MsgBox("Username atau Password salah!", MsgBoxStyle.Critical)
                txtUsername.Clear()
                txtPassword.Clear()
                txtUsername.Focus()
            End If

        Catch ex As Exception
            MsgBox("Terjadi kesalahan saat login: " & ex.Message, MsgBoxStyle.Critical)
        Finally
            conn.Close()
        End Try
    End Sub

    Private Sub btnClose_Click(sender As Object, e As EventArgs) Handles btnClose.Click
        Me.Close()
    End Sub

    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txtUsername.Focus()
    End Sub

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        FormRegister.Show()
        Me.Hide()
    End Sub

    ' ✅ Enter di Username: hanya pindah ke password
    Private Sub txtUsername_KeyDown(sender As Object, e As KeyEventArgs) Handles txtUsername.KeyDown
        If e.KeyCode = Keys.Enter Then
            e.SuppressKeyPress = True
            txtPassword.Focus()
        End If
    End Sub

    ' ✅ Enter di Password: langsung login
    Private Sub txtPassword_KeyDown(sender As Object, e As KeyEventArgs) Handles txtPassword.KeyDown
        If e.KeyCode = Keys.Enter Then
            e.SuppressKeyPress = True
            btnLogin.PerformClick()
        End If
    End Sub
End Class
