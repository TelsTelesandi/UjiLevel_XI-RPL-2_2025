<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FormAdmin
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()>
    Protected Overrides Sub Dispose(ByVal disposing As Boolean)
        Try
            If disposing AndAlso components IsNot Nothing Then
                components.Dispose()
            End If
        Finally
            MyBase.Dispose(disposing)
        End Try
    End Sub

    'Required by the Windows Form Designer
    Private components As System.ComponentModel.IContainer

    'NOTE: The following procedure is required by the Windows Form Designer
    'It can be modified using the Windows Form Designer.  
    'Do not modify it using the code editor.
    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Me.GroupBox1 = New System.Windows.Forms.GroupBox()
        Me.btn_tolak = New System.Windows.Forms.Button()
        Me.btn_setuju = New System.Windows.Forms.Button()
        Me.dtp_tglverifikasi = New System.Windows.Forms.DateTimePicker()
        Me.txt_catatanadmin = New System.Windows.Forms.TextBox()
        Me.txt_adminid = New System.Windows.Forms.TextBox()
        Me.txt_eventid = New System.Windows.Forms.TextBox()
        Me.txt_verifikasiid = New System.Windows.Forms.TextBox()
        Me.Label6 = New System.Windows.Forms.Label()
        Me.Label5 = New System.Windows.Forms.Label()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.dgv_event_pengajuan = New System.Windows.Forms.DataGridView()
        Me.dgv_verifikasi_event = New System.Windows.Forms.DataGridView()
        Me.btn_pdf = New System.Windows.Forms.Button()
        Me.btn_crud = New System.Windows.Forms.Button()
        Me.Label7 = New System.Windows.Forms.Label()
        Me.lbl_nama = New System.Windows.Forms.Label()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.Button1 = New System.Windows.Forms.Button()
        Me.btn_logout = New System.Windows.Forms.Button()
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.GroupBox1.SuspendLayout()
        CType(Me.dgv_event_pengajuan, System.ComponentModel.ISupportInitialize).BeginInit()
        CType(Me.dgv_verifikasi_event, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.Panel1.SuspendLayout()
        Me.Panel2.SuspendLayout()
        Me.SuspendLayout()
        '
        'GroupBox1
        '
        Me.GroupBox1.Controls.Add(Me.Label7)
        Me.GroupBox1.Controls.Add(Me.lbl_nama)
        Me.GroupBox1.Controls.Add(Me.btn_tolak)
        Me.GroupBox1.Controls.Add(Me.btn_setuju)
        Me.GroupBox1.Controls.Add(Me.dtp_tglverifikasi)
        Me.GroupBox1.Controls.Add(Me.txt_catatanadmin)
        Me.GroupBox1.Controls.Add(Me.txt_adminid)
        Me.GroupBox1.Controls.Add(Me.txt_eventid)
        Me.GroupBox1.Controls.Add(Me.txt_verifikasiid)
        Me.GroupBox1.Controls.Add(Me.Label6)
        Me.GroupBox1.Controls.Add(Me.Label5)
        Me.GroupBox1.Controls.Add(Me.Label4)
        Me.GroupBox1.Controls.Add(Me.Label3)
        Me.GroupBox1.Controls.Add(Me.Label2)
        Me.GroupBox1.Location = New System.Drawing.Point(212, 138)
        Me.GroupBox1.Name = "GroupBox1"
        Me.GroupBox1.Size = New System.Drawing.Size(496, 461)
        Me.GroupBox1.TabIndex = 15
        Me.GroupBox1.TabStop = False
        Me.GroupBox1.Text = "Verifikasi Event"
        '
        'btn_tolak
        '
        Me.btn_tolak.Location = New System.Drawing.Point(346, 410)
        Me.btn_tolak.Name = "btn_tolak"
        Me.btn_tolak.Size = New System.Drawing.Size(106, 41)
        Me.btn_tolak.TabIndex = 25
        Me.btn_tolak.Text = "Tolak"
        Me.btn_tolak.UseVisualStyleBackColor = True
        '
        'btn_setuju
        '
        Me.btn_setuju.Location = New System.Drawing.Point(48, 410)
        Me.btn_setuju.Name = "btn_setuju"
        Me.btn_setuju.Size = New System.Drawing.Size(106, 41)
        Me.btn_setuju.TabIndex = 16
        Me.btn_setuju.Text = "Setuju"
        Me.btn_setuju.UseVisualStyleBackColor = True
        '
        'dtp_tglverifikasi
        '
        Me.dtp_tglverifikasi.Location = New System.Drawing.Point(240, 290)
        Me.dtp_tglverifikasi.Name = "dtp_tglverifikasi"
        Me.dtp_tglverifikasi.Size = New System.Drawing.Size(212, 33)
        Me.dtp_tglverifikasi.TabIndex = 24
        '
        'txt_catatanadmin
        '
        Me.txt_catatanadmin.Location = New System.Drawing.Point(240, 352)
        Me.txt_catatanadmin.Name = "txt_catatanadmin"
        Me.txt_catatanadmin.Size = New System.Drawing.Size(212, 33)
        Me.txt_catatanadmin.TabIndex = 23
        '
        'txt_adminid
        '
        Me.txt_adminid.Location = New System.Drawing.Point(240, 228)
        Me.txt_adminid.Name = "txt_adminid"
        Me.txt_adminid.Size = New System.Drawing.Size(212, 33)
        Me.txt_adminid.TabIndex = 22
        '
        'txt_eventid
        '
        Me.txt_eventid.Location = New System.Drawing.Point(240, 166)
        Me.txt_eventid.Name = "txt_eventid"
        Me.txt_eventid.Size = New System.Drawing.Size(212, 33)
        Me.txt_eventid.TabIndex = 21
        '
        'txt_verifikasiid
        '
        Me.txt_verifikasiid.Enabled = False
        Me.txt_verifikasiid.Location = New System.Drawing.Point(240, 98)
        Me.txt_verifikasiid.Name = "txt_verifikasiid"
        Me.txt_verifikasiid.Size = New System.Drawing.Size(212, 33)
        Me.txt_verifikasiid.TabIndex = 20
        '
        'Label6
        '
        Me.Label6.AutoSize = True
        Me.Label6.Location = New System.Drawing.Point(43, 352)
        Me.Label6.Name = "Label6"
        Me.Label6.Size = New System.Drawing.Size(142, 30)
        Me.Label6.TabIndex = 19
        Me.Label6.Text = "Catatan Admin"
        '
        'Label5
        '
        Me.Label5.AutoSize = True
        Me.Label5.Location = New System.Drawing.Point(43, 290)
        Me.Label5.Name = "Label5"
        Me.Label5.Size = New System.Drawing.Size(161, 30)
        Me.Label5.TabIndex = 18
        Me.Label5.Text = "Tanggal Verifikasi"
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.Location = New System.Drawing.Point(43, 228)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(89, 30)
        Me.Label4.TabIndex = 17
        Me.Label4.Text = "Admin ID"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.Location = New System.Drawing.Point(43, 166)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(80, 30)
        Me.Label3.TabIndex = 16
        Me.Label3.Text = "Event Id"
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Location = New System.Drawing.Point(43, 98)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(108, 30)
        Me.Label2.TabIndex = 15
        Me.Label2.Text = "Verifikasi Id"
        '
        'dgv_event_pengajuan
        '
        Me.dgv_event_pengajuan.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgv_event_pengajuan.Location = New System.Drawing.Point(752, 138)
        Me.dgv_event_pengajuan.Name = "dgv_event_pengajuan"
        Me.dgv_event_pengajuan.RowHeadersWidth = 51
        Me.dgv_event_pengajuan.RowTemplate.Height = 24
        Me.dgv_event_pengajuan.Size = New System.Drawing.Size(496, 461)
        Me.dgv_event_pengajuan.TabIndex = 26
        '
        'dgv_verifikasi_event
        '
        Me.dgv_verifikasi_event.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgv_verifikasi_event.Location = New System.Drawing.Point(212, 622)
        Me.dgv_verifikasi_event.Name = "dgv_verifikasi_event"
        Me.dgv_verifikasi_event.RowHeadersWidth = 51
        Me.dgv_verifikasi_event.RowTemplate.Height = 24
        Me.dgv_verifikasi_event.Size = New System.Drawing.Size(805, 233)
        Me.dgv_verifikasi_event.TabIndex = 27
        '
        'btn_pdf
        '
        Me.btn_pdf.Location = New System.Drawing.Point(1039, 622)
        Me.btn_pdf.Name = "btn_pdf"
        Me.btn_pdf.Size = New System.Drawing.Size(209, 41)
        Me.btn_pdf.TabIndex = 30
        Me.btn_pdf.Text = "Export As PDF"
        Me.btn_pdf.UseVisualStyleBackColor = True
        '
        'btn_crud
        '
        Me.btn_crud.Location = New System.Drawing.Point(1039, 686)
        Me.btn_crud.Name = "btn_crud"
        Me.btn_crud.Size = New System.Drawing.Size(209, 41)
        Me.btn_crud.TabIndex = 31
        Me.btn_crud.Text = "CRUD User"
        Me.btn_crud.UseVisualStyleBackColor = True
        '
        'Label7
        '
        Me.Label7.AutoSize = True
        Me.Label7.Location = New System.Drawing.Point(43, 41)
        Me.Label7.Name = "Label7"
        Me.Label7.Size = New System.Drawing.Size(157, 30)
        Me.Label7.TabIndex = 31
        Me.Label7.Text = "Nama Pengguna"
        '
        'lbl_nama
        '
        Me.lbl_nama.AutoSize = True
        Me.lbl_nama.Location = New System.Drawing.Point(235, 41)
        Me.lbl_nama.Name = "lbl_nama"
        Me.lbl_nama.Size = New System.Drawing.Size(22, 30)
        Me.lbl_nama.TabIndex = 30
        Me.lbl_nama.Text = "-"
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.Navy
        Me.Panel1.Controls.Add(Me.Button1)
        Me.Panel1.Controls.Add(Me.btn_logout)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Left
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Margin = New System.Windows.Forms.Padding(2, 6, 2, 6)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(170, 890)
        Me.Panel1.TabIndex = 33
        '
        'Button1
        '
        Me.Button1.Location = New System.Drawing.Point(26, 818)
        Me.Button1.Name = "Button1"
        Me.Button1.Size = New System.Drawing.Size(112, 37)
        Me.Button1.TabIndex = 22
        Me.Button1.Text = "Logout"
        Me.Button1.UseVisualStyleBackColor = True
        '
        'btn_logout
        '
        Me.btn_logout.Location = New System.Drawing.Point(26, 1066)
        Me.btn_logout.Margin = New System.Windows.Forms.Padding(3, 4, 3, 4)
        Me.btn_logout.Name = "btn_logout"
        Me.btn_logout.Size = New System.Drawing.Size(112, 56)
        Me.btn_logout.TabIndex = 21
        Me.btn_logout.Text = "Logout"
        Me.btn_logout.UseVisualStyleBackColor = True
        '
        'Panel2
        '
        Me.Panel2.BackColor = System.Drawing.Color.Navy
        Me.Panel2.Controls.Add(Me.Label1)
        Me.Panel2.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel2.Location = New System.Drawing.Point(170, 0)
        Me.Panel2.Margin = New System.Windows.Forms.Padding(2, 6, 2, 6)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(1110, 110)
        Me.Panel2.TabIndex = 34
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Poppins Medium", 13.8!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.White
        Me.Label1.Location = New System.Drawing.Point(335, 35)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(440, 40)
        Me.Label1.TabIndex = 3
        Me.Label1.Text = "Aplikasi Pengajuan Proposal V 1.0"
        '
        'FormAdmin
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(10.0!, 30.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1280, 890)
        Me.Controls.Add(Me.Panel2)
        Me.Controls.Add(Me.Panel1)
        Me.Controls.Add(Me.btn_crud)
        Me.Controls.Add(Me.btn_pdf)
        Me.Controls.Add(Me.dgv_verifikasi_event)
        Me.Controls.Add(Me.dgv_event_pengajuan)
        Me.Controls.Add(Me.GroupBox1)
        Me.Font = New System.Drawing.Font("Poppins", 10.2!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Margin = New System.Windows.Forms.Padding(4, 6, 4, 6)
        Me.Name = "FormAdmin"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "FormAdmin"
        Me.GroupBox1.ResumeLayout(False)
        Me.GroupBox1.PerformLayout()
        CType(Me.dgv_event_pengajuan, System.ComponentModel.ISupportInitialize).EndInit()
        CType(Me.dgv_verifikasi_event, System.ComponentModel.ISupportInitialize).EndInit()
        Me.Panel1.ResumeLayout(False)
        Me.Panel2.ResumeLayout(False)
        Me.Panel2.PerformLayout()
        Me.ResumeLayout(False)

    End Sub
    Friend WithEvents GroupBox1 As GroupBox
    Friend WithEvents dgv_event_pengajuan As DataGridView
    Friend WithEvents btn_tolak As Button
    Friend WithEvents btn_setuju As Button
    Friend WithEvents dtp_tglverifikasi As DateTimePicker
    Friend WithEvents txt_catatanadmin As TextBox
    Friend WithEvents txt_adminid As TextBox
    Friend WithEvents txt_eventid As TextBox
    Friend WithEvents txt_verifikasiid As TextBox
    Friend WithEvents Label6 As Label
    Friend WithEvents Label5 As Label
    Friend WithEvents Label4 As Label
    Friend WithEvents Label3 As Label
    Friend WithEvents Label2 As Label
    Friend WithEvents dgv_verifikasi_event As DataGridView
    Friend WithEvents btn_pdf As Button
    Friend WithEvents btn_crud As Button
    Friend WithEvents Label7 As Label
    Friend WithEvents lbl_nama As Label
    Friend WithEvents Panel1 As Panel
    Friend WithEvents Button1 As Button
    Friend WithEvents btn_logout As Button
    Friend WithEvents Panel2 As Panel
    Friend WithEvents Label1 As Label
End Class
