Imports System.Data.SqlClient
Public Class FormBuatAkun
    Private connectionString As String = "Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True"
    Private Sub FormBuatAkun_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        txt_id.Enabled = False
    End Sub

    Private Sub btn_close_Click(sender As Object, e As EventArgs) Handles btn_back.Click
        FormLogin.Show()
        FormLogin.txt_username.Text = ""
        FormLogin.txt_password.Text = ""
        Me.Hide()
    End Sub

    Private Sub btn_reset_Click(sender As Object, e As EventArgs) Handles btn_reset.Click
        txt_id.Clear()
        txt_username.Clear()
        txt_password.Clear()
        txt_role.Clear()
        txt_namalengkap.Clear()
        txt_eskul.Clear()
        txt_id.Focus()
    End Sub

    Private Sub btn_buatakun_Click(sender As Object, e As EventArgs) Handles btn_buatakun.Click
        Dim conn As New SqlConnection("Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True")

        If txt_username.Text = "" Or txt_password.Text = "" Or txt_role.Text = "" Or txt_namalengkap.Text = "" Then
            MessageBox.Show("Kolom wajib (Username, Password, Role, Nama Lengkap) harus diisi.", "Peringatan", MessageBoxButtons.OK, MessageBoxIcon.Warning)
            Exit Sub
        End If

        Dim EkskulValue As Object
        If txt_role.Text.ToLower() = "admin" Then
            EkskulValue = DBNull.Value
        Else
            EkskulValue = txt_eskul.Text
        End If

        Try
            conn.Open()
            Dim cmd As New SqlCommand("INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (@username, @password, @role, @nama_lengkap, @ekskul); SELECT SCOPE_IDENTITY()", conn)
            cmd.Parameters.AddWithValue("@username", txt_username.Text)
            cmd.Parameters.AddWithValue("@password", txt_password.Text)
            cmd.Parameters.AddWithValue("@role", txt_role.Text)
            cmd.Parameters.AddWithValue("@nama_lengkap", txt_namalengkap.Text)
            cmd.Parameters.AddWithValue("@ekskul", EkskulValue)

            ' Eksekusi dan ambil user_id yang baru dibuat
            Dim newUserId As Integer = Convert.ToInt32(cmd.ExecuteScalar())

            MessageBox.Show("Akun berhasil dibuat! User ID: " & newUserId, "Sukses", MessageBoxButtons.OK, MessageBoxIcon.Information)

            ' Bersihkan form
            txt_username.Clear()
            txt_password.Clear()
            txt_role.Clear()
            txt_namalengkap.Clear()
            txt_eskul.Clear()

        Catch ex As Exception
            MessageBox.Show("Terjadi kesalahan: " & ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Error)
        Finally
            conn.Close()
        End Try
    End Sub
End Class