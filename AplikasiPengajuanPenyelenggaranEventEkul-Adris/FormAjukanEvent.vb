Imports System.IO
Imports MySql.Data.MySqlClient

Public Class FormAjukanEvent
    Public user_id As Integer
    Private filePath As String = ""
    Private Sub FormAjukanEvent_Load(sender As Object, e As EventArgs) Handles MyBase.Load

    End Sub

    Private Sub TextBox1_TextChanged(sender As Object, e As EventArgs) Handles txtJudulEvent.TextChanged

    End Sub

    Private Sub Label2_Click(sender As Object, e As EventArgs) Handles Label2.Click

    End Sub

    Private Sub Label3_Click(sender As Object, e As EventArgs) Handles Label3.Click

    End Sub

    Private Sub Label6_Click(sender As Object, e As EventArgs) Handles Label6.Click

    End Sub

    Private Sub btnBrowse_Click(sender As Object, e As EventArgs) Handles btnBrowse.Click
        Dim ofd As New OpenFileDialog()
        ofd.Filter = "PDF Files (*.pdf)|*.pdf"
        ofd.Title = "Pilih File Proposal"

        If ofd.ShowDialog() = DialogResult.OK Then
            filePath = ofd.FileName
            lblFilePath.Text = Path.GetFileName(filePath)
        End If
    End Sub


    Private Sub btnAjukanEvent_Click(sender As Object, e As EventArgs) Handles btnAjukanEvent.Click
        If txtJudulEvent.Text = "" Or txtJudulKegiatan.Text = "" Or txtPembiayaan.Text = "" Or txtDeskripsi.Text = "" Or filePath = "" Then
            MsgBox("Semua field wajib diisi!", MsgBoxStyle.Exclamation)
            Exit Sub
        End If

        Try
            Koneksi()
            Dim query As String = "INSERT INTO event_pengajuan (user_id, judul_event, jenis_kegiatan, total_pembiayaan, proposal, deskripsi, tanggal_pengajuan, status) 
                           VALUES (@user_id, @judul_event, @jenis_kegiatan, @pembiayaan, @proposal, @deskripsi, CURDATE(), 'request')"
            cmd = New MySqlCommand(query, conn)
            cmd.Parameters.AddWithValue("@user_id", Me.user_id)
            cmd.Parameters.AddWithValue("@judul_event", txtJudulEvent.Text)
            cmd.Parameters.AddWithValue("@jenis_kegiatan", txtJudulKegiatan.Text)
            cmd.Parameters.AddWithValue("@pembiayaan", txtPembiayaan.Text)
            cmd.Parameters.AddWithValue("@proposal", filePath)
            cmd.Parameters.AddWithValue("@deskripsi", txtDeskripsi.Text)

            cmd.ExecuteNonQuery()
            MsgBox("Event berhasil diajukan!", MsgBoxStyle.Information)

            ' Reset form
            txtJudulEvent.Clear()
            txtJudulKegiatan.Clear()
            txtPembiayaan.Clear()
            txtDeskripsi.Clear()
            filePath = ""
            lblFilePath.Text = ""

            conn.Close()

            ' Kembali ke dashboard
            Dim dashboard As New FormDashboardUser()
            dashboard.user_id = Me.user_id ' jika ingin teruskan user_id ke dashboard
            dashboard.Show()
            Me.Close()

        Catch ex As Exception
            MsgBox("Gagal mengajukan event: " & ex.Message, MsgBoxStyle.Critical)
        End Try
    End Sub

    Private Sub Button1_Click(sender As Object, e As EventArgs) Handles Button1.Click
        FormDashboardUser.Show()
        Me.Close()
    End Sub
End Class