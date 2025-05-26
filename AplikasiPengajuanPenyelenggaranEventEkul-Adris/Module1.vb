Imports MySql.Data.MySqlClient

Module Module1
    Public conn As MySqlConnection
    Public cmd As MySqlCommand
    Public dr As MySqlDataReader

    Public Sub Koneksi()
        Dim connectionString As String = "server=localhost;userid=root;password=;database=re_adris"
        conn = New MySqlConnection(connectionString)
        Try
            conn.Open()
        Catch ex As Exception
            MsgBox("Gagal terkoneksi ke database: " & ex.Message)
        End Try
    End Sub
End Module
