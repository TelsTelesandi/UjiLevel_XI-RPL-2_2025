Imports System.Data.SqlClient
Public Class CRUDUser
    Sub TampilData()
        Call Koneksi()
        da = New SqlDataAdapter("SELECT * FROM users", conn)
        ds = New DataSet()
        da.Fill(ds, "users")
        dgv_crud_user.DataSource = ds.Tables("users")
        conn.Close()

        With dgv_crud_user
            .Columns("user_id").HeaderText = "User ID"
            .Columns("username").HeaderText = "Username"
            .Columns("password").HeaderText = "Password"
            .Columns("role").HeaderText = "Role"
            .Columns("nama_lengkap").HeaderText = "Nama Lengkap"
            .Columns("Ekskul").HeaderText = "Ekskul"
        End With
    End Sub
    Sub KosongkanForm()
        txt_id.Clear()
        txt_username.Clear()
        txt_password.Clear()
        txt_role.Clear()
        txt_namalengkap.Clear()
        txt_eskul.Clear()
    End Sub
    Private Sub btn_back_Click(sender As Object, e As EventArgs) Handles btn_back.Click
        FormAdmin.Show()
        Me.Hide()
    End Sub

    Private Sub CRUDUser_Load(sender As Object, e As EventArgs) Handles MyBase.Load
        TampilData()
    End Sub

    Private Sub btn_buatakun_Click(sender As Object, e As EventArgs) Handles btn_buatakun.Click
        Call Koneksi()

        Dim query As String = "INSERT INTO users (username, password, role, nama_lengkap, Ekskul) VALUES (@username, @password, @role, @nama_lengkap, @ekskul)"

        cmd = New SqlCommand(query, conn)

        cmd.Parameters.AddWithValue("@username", txt_username.Text)
        cmd.Parameters.AddWithValue("@password", txt_password.Text)
        cmd.Parameters.AddWithValue("@role", txt_role.Text)
        cmd.Parameters.AddWithValue("@nama_lengkap", txt_namalengkap.Text)
        cmd.Parameters.AddWithValue("@ekskul", txt_eskul.Text)

        Dim rowsAffected As Integer = cmd.ExecuteNonQuery()

        If rowsAffected > 0 Then
            MsgBox("Akun berhasil dibuat.")
            TampilData()
            KosongkanForm()
        Else
            MsgBox("Gagal membuat akun.")
        End If

        conn.Close()
    End Sub

    Private Sub btn_edit_Click(sender As Object, e As EventArgs) Handles btn_edit.Click
        Call Koneksi()
        Dim query As String = "UPDATE users SET username=@username, password=@password, role=@role, nama_lengkap=@nama_lengkap, Ekskul=@Ekskul WHERE user_id=@id"
        cmd = New SqlCommand(query, conn)
        cmd.Parameters.AddWithValue("@username", txt_username.Text)
        cmd.Parameters.AddWithValue("@password", txt_password.Text)
        cmd.Parameters.AddWithValue("@role", txt_role.Text)
        cmd.Parameters.AddWithValue("@nama_lengkap", txt_namalengkap.Text)
        cmd.Parameters.AddWithValue("@Ekskul", txt_eskul.Text)
        cmd.Parameters.AddWithValue("@id", txt_id.Text)
        cmd.ExecuteNonQuery()
        MsgBox("Data berhasil diupdate.")
        TampilData()
        KosongkanForm()
        conn.Close()
    End Sub

    Private Sub btn_delete_Click(sender As Object, e As EventArgs) Handles btn_delete.Click
        Call Koneksi()
        Dim query As String = "DELETE FROM users WHERE user_id=@id"
        cmd = New SqlCommand(query, conn)
        cmd.Parameters.AddWithValue("@id", txt_id.Text)
        cmd.ExecuteNonQuery()
        MsgBox("Data berhasil dihapus.")
        TampilData()
        KosongkanForm()
        conn.Close()
    End Sub

    Private Sub dgv_crud_user_CellContentClick(sender As Object, e As DataGridViewCellEventArgs) Handles dgv_crud_user.CellContentClick
        Dim i As Integer = dgv_crud_user.CurrentRow.Index
        txt_id.Text = dgv_crud_user.Item(0, i).Value.ToString()
        txt_username.Text = dgv_crud_user.Item(1, i).Value.ToString()
        txt_password.Text = dgv_crud_user.Item(2, i).Value.ToString()
        txt_role.Text = dgv_crud_user.Item(3, i).Value.ToString()
        txt_namalengkap.Text = dgv_crud_user.Item(4, i).Value.ToString()

        If IsDBNull(dgv_crud_user.Item(5, i).Value) Then
            txt_eskul.Text = ""
        Else
            txt_eskul.Text = dgv_crud_user.Item(5, i).Value.ToString()
        End If
    End Sub
End Class