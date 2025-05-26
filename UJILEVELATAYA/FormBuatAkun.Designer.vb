<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FormBuatAkun
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
        Dim resources As System.ComponentModel.ComponentResourceManager = New System.ComponentModel.ComponentResourceManager(GetType(FormBuatAkun))
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btn_back = New System.Windows.Forms.Button()
        Me.btn_reset = New System.Windows.Forms.Button()
        Me.btn_buatakun = New System.Windows.Forms.Button()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.txt_username = New System.Windows.Forms.TextBox()
        Me.txt_password = New System.Windows.Forms.TextBox()
        Me.txt_id = New System.Windows.Forms.TextBox()
        Me.Label5 = New System.Windows.Forms.Label()
        Me.Label6 = New System.Windows.Forms.Label()
        Me.txt_role = New System.Windows.Forms.TextBox()
        Me.PictureBox1 = New System.Windows.Forms.PictureBox()
        Me.Label7 = New System.Windows.Forms.Label()
        Me.txt_eskul = New System.Windows.Forms.TextBox()
        Me.Label8 = New System.Windows.Forms.Label()
        Me.txt_namalengkap = New System.Windows.Forms.TextBox()
        Me.Panel2.SuspendLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'Panel2
        '
        Me.Panel2.BackColor = System.Drawing.Color.Navy
        Me.Panel2.Controls.Add(Me.Label1)
        Me.Panel2.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel2.Location = New System.Drawing.Point(170, 0)
        Me.Panel2.Margin = New System.Windows.Forms.Padding(5, 11, 5, 11)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(680, 110)
        Me.Panel2.TabIndex = 3
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Poppins Medium", 13.8!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.White
        Me.Label1.Location = New System.Drawing.Point(120, 30)
        Me.Label1.Margin = New System.Windows.Forms.Padding(4, 0, 4, 0)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(440, 40)
        Me.Label1.TabIndex = 0
        Me.Label1.Text = "Aplikasi Pengajuan Proposal V 1.0"
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.Navy
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Left
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Margin = New System.Windows.Forms.Padding(5, 11, 5, 11)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(170, 600)
        Me.Panel1.TabIndex = 2
        '
        'btn_back
        '
        Me.btn_back.Location = New System.Drawing.Point(706, 541)
        Me.btn_back.Name = "btn_back"
        Me.btn_back.Size = New System.Drawing.Size(112, 37)
        Me.btn_back.TabIndex = 20
        Me.btn_back.Text = "Back"
        Me.btn_back.UseVisualStyleBackColor = True
        '
        'btn_reset
        '
        Me.btn_reset.Location = New System.Drawing.Point(576, 541)
        Me.btn_reset.Name = "btn_reset"
        Me.btn_reset.Size = New System.Drawing.Size(112, 37)
        Me.btn_reset.TabIndex = 19
        Me.btn_reset.Text = "Reset"
        Me.btn_reset.UseVisualStyleBackColor = True
        '
        'btn_buatakun
        '
        Me.btn_buatakun.Location = New System.Drawing.Point(443, 541)
        Me.btn_buatakun.Name = "btn_buatakun"
        Me.btn_buatakun.Size = New System.Drawing.Size(112, 37)
        Me.btn_buatakun.TabIndex = 18
        Me.btn_buatakun.Text = "Buat Akun"
        Me.btn_buatakun.UseVisualStyleBackColor = True
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label4.Location = New System.Drawing.Point(439, 311)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(94, 30)
        Me.Label4.TabIndex = 17
        Me.Label4.Text = "Password"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label3.Location = New System.Drawing.Point(440, 249)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(101, 30)
        Me.Label3.TabIndex = 16
        Me.Label3.Text = "Username"
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label2.Location = New System.Drawing.Point(438, 191)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(28, 30)
        Me.Label2.TabIndex = 15
        Me.Label2.Text = "Id"
        '
        'txt_username
        '
        Me.txt_username.Location = New System.Drawing.Point(611, 246)
        Me.txt_username.Name = "txt_username"
        Me.txt_username.Size = New System.Drawing.Size(207, 33)
        Me.txt_username.TabIndex = 14
        '
        'txt_password
        '
        Me.txt_password.Location = New System.Drawing.Point(611, 308)
        Me.txt_password.Name = "txt_password"
        Me.txt_password.Size = New System.Drawing.Size(207, 33)
        Me.txt_password.TabIndex = 13
        '
        'txt_id
        '
        Me.txt_id.Location = New System.Drawing.Point(611, 188)
        Me.txt_id.Name = "txt_id"
        Me.txt_id.Size = New System.Drawing.Size(207, 33)
        Me.txt_id.TabIndex = 12
        '
        'Label5
        '
        Me.Label5.AutoSize = True
        Me.Label5.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label5.Location = New System.Drawing.Point(440, 367)
        Me.Label5.Name = "Label5"
        Me.Label5.Size = New System.Drawing.Size(49, 30)
        Me.Label5.TabIndex = 22
        Me.Label5.Text = "Role"
        '
        'Label6
        '
        Me.Label6.AutoSize = True
        Me.Label6.Font = New System.Drawing.Font("Poppins Medium", 12.0!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label6.Location = New System.Drawing.Point(442, 131)
        Me.Label6.Name = "Label6"
        Me.Label6.Size = New System.Drawing.Size(127, 36)
        Me.Label6.TabIndex = 23
        Me.Label6.Text = "Buat Akun"
        '
        'txt_role
        '
        Me.txt_role.Location = New System.Drawing.Point(611, 364)
        Me.txt_role.Name = "txt_role"
        Me.txt_role.Size = New System.Drawing.Size(207, 33)
        Me.txt_role.TabIndex = 21
        '
        'PictureBox1
        '
        Me.PictureBox1.Image = CType(resources.GetObject("PictureBox1.Image"), System.Drawing.Image)
        Me.PictureBox1.Location = New System.Drawing.Point(199, 242)
        Me.PictureBox1.Name = "PictureBox1"
        Me.PictureBox1.Size = New System.Drawing.Size(211, 209)
        Me.PictureBox1.SizeMode = System.Windows.Forms.PictureBoxSizeMode.Zoom
        Me.PictureBox1.TabIndex = 24
        Me.PictureBox1.TabStop = False
        '
        'Label7
        '
        Me.Label7.AutoSize = True
        Me.Label7.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label7.Location = New System.Drawing.Point(438, 478)
        Me.Label7.Name = "Label7"
        Me.Label7.Size = New System.Drawing.Size(55, 30)
        Me.Label7.TabIndex = 34
        Me.Label7.Text = "Eskul"
        '
        'txt_eskul
        '
        Me.txt_eskul.Location = New System.Drawing.Point(611, 478)
        Me.txt_eskul.Name = "txt_eskul"
        Me.txt_eskul.Size = New System.Drawing.Size(207, 33)
        Me.txt_eskul.TabIndex = 33
        '
        'Label8
        '
        Me.Label8.AutoSize = True
        Me.Label8.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label8.Location = New System.Drawing.Point(438, 421)
        Me.Label8.Name = "Label8"
        Me.Label8.Size = New System.Drawing.Size(141, 30)
        Me.Label8.TabIndex = 32
        Me.Label8.Text = "Nama Lengkap"
        '
        'txt_namalengkap
        '
        Me.txt_namalengkap.Location = New System.Drawing.Point(611, 418)
        Me.txt_namalengkap.Name = "txt_namalengkap"
        Me.txt_namalengkap.Size = New System.Drawing.Size(207, 33)
        Me.txt_namalengkap.TabIndex = 31
        '
        'FormBuatAkun
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(10.0!, 30.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(850, 600)
        Me.Controls.Add(Me.Label7)
        Me.Controls.Add(Me.txt_eskul)
        Me.Controls.Add(Me.Label8)
        Me.Controls.Add(Me.txt_namalengkap)
        Me.Controls.Add(Me.PictureBox1)
        Me.Controls.Add(Me.Label6)
        Me.Controls.Add(Me.Label5)
        Me.Controls.Add(Me.txt_role)
        Me.Controls.Add(Me.btn_back)
        Me.Controls.Add(Me.btn_reset)
        Me.Controls.Add(Me.btn_buatakun)
        Me.Controls.Add(Me.Label4)
        Me.Controls.Add(Me.Label3)
        Me.Controls.Add(Me.Label2)
        Me.Controls.Add(Me.txt_username)
        Me.Controls.Add(Me.txt_password)
        Me.Controls.Add(Me.txt_id)
        Me.Controls.Add(Me.Panel2)
        Me.Controls.Add(Me.Panel1)
        Me.Font = New System.Drawing.Font("Poppins", 10.2!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Margin = New System.Windows.Forms.Padding(4, 6, 4, 6)
        Me.Name = "FormBuatAkun"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "FormBuatAkun"
        Me.Panel2.ResumeLayout(False)
        Me.Panel2.PerformLayout()
        CType(Me.PictureBox1, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub

    Friend WithEvents Panel2 As Panel
    Friend WithEvents Label1 As Label
    Friend WithEvents Panel1 As Panel
    Friend WithEvents btn_back As Button
    Friend WithEvents btn_reset As Button
    Friend WithEvents btn_buatakun As Button
    Friend WithEvents Label4 As Label
    Friend WithEvents Label3 As Label
    Friend WithEvents Label2 As Label
    Friend WithEvents txt_username As TextBox
    Friend WithEvents txt_password As TextBox
    Friend WithEvents txt_id As TextBox
    Friend WithEvents Label5 As Label
    Friend WithEvents Label6 As Label
    Friend WithEvents txt_role As TextBox
    Friend WithEvents PictureBox1 As PictureBox
    Friend WithEvents Label7 As Label
    Friend WithEvents txt_eskul As TextBox
    Friend WithEvents Label8 As Label
    Friend WithEvents txt_namalengkap As TextBox
End Class
