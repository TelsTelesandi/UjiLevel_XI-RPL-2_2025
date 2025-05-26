<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class CRUDUser
    Inherits System.Windows.Forms.Form

    'Form overrides dispose to clean up the component list.
    <System.Diagnostics.DebuggerNonUserCode()> _
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
    <System.Diagnostics.DebuggerStepThrough()> _
    Private Sub InitializeComponent()
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btn_back = New System.Windows.Forms.Button()
        Me.gb_data_pengguna = New System.Windows.Forms.GroupBox()
        Me.btn_delete = New System.Windows.Forms.Button()
        Me.btn_edit = New System.Windows.Forms.Button()
        Me.Label7 = New System.Windows.Forms.Label()
        Me.txt_password = New System.Windows.Forms.TextBox()
        Me.btn_buatakun = New System.Windows.Forms.Button()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.txt_eskul = New System.Windows.Forms.TextBox()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.txt_username = New System.Windows.Forms.TextBox()
        Me.Label8 = New System.Windows.Forms.Label()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.txt_id = New System.Windows.Forms.TextBox()
        Me.txt_namalengkap = New System.Windows.Forms.TextBox()
        Me.txt_role = New System.Windows.Forms.TextBox()
        Me.Label5 = New System.Windows.Forms.Label()
        Me.dgv_crud_user = New System.Windows.Forms.DataGridView()
        Me.Panel2.SuspendLayout()
        Me.Panel1.SuspendLayout()
        Me.gb_data_pengguna.SuspendLayout()
        CType(Me.dgv_crud_user, System.ComponentModel.ISupportInitialize).BeginInit()
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
        Me.Panel2.Size = New System.Drawing.Size(1110, 110)
        Me.Panel2.TabIndex = 5
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Poppins Medium", 13.8!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.White
        Me.Label1.Location = New System.Drawing.Point(335, 35)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(440, 40)
        Me.Label1.TabIndex = 2
        Me.Label1.Text = "Aplikasi Pengajuan Proposal V 1.0"
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.Navy
        Me.Panel1.Controls.Add(Me.btn_back)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Left
        Me.Panel1.Location = New System.Drawing.Point(0, 0)
        Me.Panel1.Margin = New System.Windows.Forms.Padding(5, 11, 5, 11)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(170, 890)
        Me.Panel1.TabIndex = 4
        '
        'btn_back
        '
        Me.btn_back.Location = New System.Drawing.Point(23, 814)
        Me.btn_back.Name = "btn_back"
        Me.btn_back.Size = New System.Drawing.Size(112, 37)
        Me.btn_back.TabIndex = 23
        Me.btn_back.Text = "Back"
        Me.btn_back.UseVisualStyleBackColor = True
        '
        'gb_data_pengguna
        '
        Me.gb_data_pengguna.Controls.Add(Me.btn_delete)
        Me.gb_data_pengguna.Controls.Add(Me.btn_edit)
        Me.gb_data_pengguna.Controls.Add(Me.Label7)
        Me.gb_data_pengguna.Controls.Add(Me.txt_password)
        Me.gb_data_pengguna.Controls.Add(Me.btn_buatakun)
        Me.gb_data_pengguna.Controls.Add(Me.Label2)
        Me.gb_data_pengguna.Controls.Add(Me.txt_eskul)
        Me.gb_data_pengguna.Controls.Add(Me.Label3)
        Me.gb_data_pengguna.Controls.Add(Me.txt_username)
        Me.gb_data_pengguna.Controls.Add(Me.Label8)
        Me.gb_data_pengguna.Controls.Add(Me.Label4)
        Me.gb_data_pengguna.Controls.Add(Me.txt_id)
        Me.gb_data_pengguna.Controls.Add(Me.txt_namalengkap)
        Me.gb_data_pengguna.Controls.Add(Me.txt_role)
        Me.gb_data_pengguna.Controls.Add(Me.Label5)
        Me.gb_data_pengguna.Location = New System.Drawing.Point(233, 125)
        Me.gb_data_pengguna.Name = "gb_data_pengguna"
        Me.gb_data_pengguna.Size = New System.Drawing.Size(978, 396)
        Me.gb_data_pengguna.TabIndex = 7
        Me.gb_data_pengguna.TabStop = False
        Me.gb_data_pengguna.Text = "CRUD Data Pengguna"
        '
        'btn_delete
        '
        Me.btn_delete.Location = New System.Drawing.Point(803, 334)
        Me.btn_delete.Name = "btn_delete"
        Me.btn_delete.Size = New System.Drawing.Size(112, 37)
        Me.btn_delete.TabIndex = 61
        Me.btn_delete.Text = "Delete"
        Me.btn_delete.UseVisualStyleBackColor = True
        '
        'btn_edit
        '
        Me.btn_edit.Location = New System.Drawing.Point(673, 334)
        Me.btn_edit.Name = "btn_edit"
        Me.btn_edit.Size = New System.Drawing.Size(112, 37)
        Me.btn_edit.TabIndex = 60
        Me.btn_edit.Text = "Edit"
        Me.btn_edit.UseVisualStyleBackColor = True
        '
        'Label7
        '
        Me.Label7.AutoSize = True
        Me.Label7.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label7.Location = New System.Drawing.Point(30, 334)
        Me.Label7.Name = "Label7"
        Me.Label7.Size = New System.Drawing.Size(55, 30)
        Me.Label7.TabIndex = 58
        Me.Label7.Text = "Eskul"
        '
        'txt_password
        '
        Me.txt_password.Location = New System.Drawing.Point(203, 164)
        Me.txt_password.Name = "txt_password"
        Me.txt_password.Size = New System.Drawing.Size(165, 33)
        Me.txt_password.TabIndex = 48
        '
        'btn_buatakun
        '
        Me.btn_buatakun.Location = New System.Drawing.Point(540, 334)
        Me.btn_buatakun.Name = "btn_buatakun"
        Me.btn_buatakun.Size = New System.Drawing.Size(112, 37)
        Me.btn_buatakun.TabIndex = 59
        Me.btn_buatakun.Text = "Buat Akun"
        Me.btn_buatakun.UseVisualStyleBackColor = True
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label2.Location = New System.Drawing.Point(30, 47)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(28, 30)
        Me.Label2.TabIndex = 50
        Me.Label2.Text = "Id"
        '
        'txt_eskul
        '
        Me.txt_eskul.Location = New System.Drawing.Point(203, 334)
        Me.txt_eskul.Name = "txt_eskul"
        Me.txt_eskul.Size = New System.Drawing.Size(165, 33)
        Me.txt_eskul.TabIndex = 57
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label3.Location = New System.Drawing.Point(32, 105)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(101, 30)
        Me.Label3.TabIndex = 51
        Me.Label3.Text = "Username"
        '
        'txt_username
        '
        Me.txt_username.Location = New System.Drawing.Point(203, 102)
        Me.txt_username.Name = "txt_username"
        Me.txt_username.Size = New System.Drawing.Size(165, 33)
        Me.txt_username.TabIndex = 49
        '
        'Label8
        '
        Me.Label8.AutoSize = True
        Me.Label8.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label8.Location = New System.Drawing.Point(30, 277)
        Me.Label8.Name = "Label8"
        Me.Label8.Size = New System.Drawing.Size(141, 30)
        Me.Label8.TabIndex = 56
        Me.Label8.Text = "Nama Lengkap"
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label4.Location = New System.Drawing.Point(31, 167)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(94, 30)
        Me.Label4.TabIndex = 52
        Me.Label4.Text = "Password"
        '
        'txt_id
        '
        Me.txt_id.Location = New System.Drawing.Point(203, 44)
        Me.txt_id.Name = "txt_id"
        Me.txt_id.Size = New System.Drawing.Size(165, 33)
        Me.txt_id.TabIndex = 47
        '
        'txt_namalengkap
        '
        Me.txt_namalengkap.Location = New System.Drawing.Point(203, 274)
        Me.txt_namalengkap.Name = "txt_namalengkap"
        Me.txt_namalengkap.Size = New System.Drawing.Size(165, 33)
        Me.txt_namalengkap.TabIndex = 55
        '
        'txt_role
        '
        Me.txt_role.Location = New System.Drawing.Point(203, 220)
        Me.txt_role.Name = "txt_role"
        Me.txt_role.Size = New System.Drawing.Size(165, 33)
        Me.txt_role.TabIndex = 53
        '
        'Label5
        '
        Me.Label5.AutoSize = True
        Me.Label5.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.Label5.Location = New System.Drawing.Point(32, 223)
        Me.Label5.Name = "Label5"
        Me.Label5.Size = New System.Drawing.Size(49, 30)
        Me.Label5.TabIndex = 54
        Me.Label5.Text = "Role"
        '
        'dgv_crud_user
        '
        Me.dgv_crud_user.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgv_crud_user.Location = New System.Drawing.Point(233, 554)
        Me.dgv_crud_user.Name = "dgv_crud_user"
        Me.dgv_crud_user.RowHeadersWidth = 51
        Me.dgv_crud_user.RowTemplate.Height = 24
        Me.dgv_crud_user.Size = New System.Drawing.Size(978, 297)
        Me.dgv_crud_user.TabIndex = 8
        '
        'CRUDUser
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(10.0!, 30.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1280, 890)
        Me.Controls.Add(Me.dgv_crud_user)
        Me.Controls.Add(Me.gb_data_pengguna)
        Me.Controls.Add(Me.Panel2)
        Me.Controls.Add(Me.Panel1)
        Me.Font = New System.Drawing.Font("Poppins", 10.2!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Margin = New System.Windows.Forms.Padding(4, 6, 4, 6)
        Me.Name = "CRUDUser"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "CRUDUser"
        Me.Panel2.ResumeLayout(False)
        Me.Panel2.PerformLayout()
        Me.Panel1.ResumeLayout(False)
        Me.gb_data_pengguna.ResumeLayout(False)
        Me.gb_data_pengguna.PerformLayout()
        CType(Me.dgv_crud_user, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents Panel2 As Panel
    Friend WithEvents Panel1 As Panel
    Friend WithEvents btn_back As Button
    Friend WithEvents gb_data_pengguna As GroupBox
    Friend WithEvents Label7 As Label
    Friend WithEvents txt_password As TextBox
    Friend WithEvents Label2 As Label
    Friend WithEvents txt_eskul As TextBox
    Friend WithEvents Label3 As Label
    Friend WithEvents txt_username As TextBox
    Friend WithEvents Label8 As Label
    Friend WithEvents Label4 As Label
    Friend WithEvents txt_id As TextBox
    Friend WithEvents txt_namalengkap As TextBox
    Friend WithEvents txt_role As TextBox
    Friend WithEvents Label5 As Label
    Friend WithEvents btn_delete As Button
    Friend WithEvents btn_edit As Button
    Friend WithEvents btn_buatakun As Button
    Friend WithEvents dgv_crud_user As DataGridView
    Friend WithEvents Label1 As Label
End Class
