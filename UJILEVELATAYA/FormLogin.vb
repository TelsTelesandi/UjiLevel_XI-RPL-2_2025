Imports System.Data.SqlClient
Public Class FormLogin
    Private connectionString As String = "Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True"
    Private Sub Form1_Load(sender As Object, e As EventArgs) Handles MyBase.Load

    End Sub
    Private Sub btn_close_Click(sender As Object, e As EventArgs) Handles btn_close.Click
        Me.Close()
    End Sub

    Private Sub LinkLabel1_LinkClicked(sender As Object, e As LinkLabelLinkClickedEventArgs) Handles LinkLabel1.LinkClicked
        FormBuatAkun.Show()
        FormBuatAkun.txt_id.Text = ""
        FormBuatAkun.txt_username.Text = ""
        FormBuatAkun.txt_password.Text = ""
        FormBuatAkun.txt_role.Text = ""
        FormBuatAkun.txt_namalengkap.Text = ""
        FormBuatAkun.txt_eskul.Text = ""
        Me.Hide()
    End Sub

    Private Sub btn_reset_Click(sender As Object, e As EventArgs) Handles btn_reset.Click
        txt_id.Clear()
        txt_username.Clear()
        txt_password.Clear()
        txt_id.Focus()
    End Sub

    Private Sub btn_login_Click(sender As Object, e As EventArgs) Handles btn_login.Click
        If txt_id.Text.Trim() = "" Or txt_username.Text.Trim() = "" Or txt_password.Text.Trim() = "" Then
            MessageBox.Show("Harap isi semua field!", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Exit Sub
        End If

        Try
            Dim conn As New SqlConnection("Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True")
            conn.Open()

            Dim cmd As New SqlCommand("SELECT * FROM users WHERE user_id=@id AND username=@username AND password=@password", conn)
            cmd.Parameters.AddWithValue("@id", txt_id.Text)
            cmd.Parameters.AddWithValue("@username", txt_username.Text)
            cmd.Parameters.AddWithValue("@password", txt_password.Text)

            Dim dr As SqlDataReader = cmd.ExecuteReader()

            If dr.Read() Then
                Dim role As String = dr("role").ToString()
                Dim namaLengkap As String = dr("nama_lengkap").ToString()
                Dim ekskul As String = dr("Ekskul").ToString()

                MessageBox.Show("Login berhasil sebagai " & role, "Info", MessageBoxButtons.OK, MessageBoxIcon.Information)

                If role = "Admin" Then
                    Dim f As New FormAdmin()
                    f.lbl_nama.Text = namaLengkap
                    f.Show()
                Else
                    Dim f As New FormUser()
                    f.lbl_nama.Text = namaLengkap
                    f.lbl_eskul.Text = ekskul
                    f.Show()
                End If

                Me.Hide()
            Else
                MessageBox.Show("Username atau password salah.", "Login Gagal", MessageBoxButtons.OK, MessageBoxIcon.Error)
            End If

            conn.Close()

        Catch ex As Exception
            MessageBox.Show("Terjadi kesalahan: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        End Try
    End Sub
End Class
