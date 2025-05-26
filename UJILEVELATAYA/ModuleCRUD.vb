Imports System.Data.SqlClient
Module ModuleCRUD
    Public conn As SqlConnection
    Public cmd As SqlCommand
    Public dr As SqlDataReader
    Public da As SqlDataAdapter
    Public ds As DataSet
    Public Sub Koneksi()
        conn = New SqlConnection("Data Source=.\SQLEXPRESS;Initial Catalog=DB_UJILEVEL;Integrated Security=True")
        If conn.State = ConnectionState.Closed Then
            conn.Open()
        End If
    End Sub
End Module
