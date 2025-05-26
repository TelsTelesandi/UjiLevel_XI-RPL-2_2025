<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FormUser
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
    <System.Diagnostics.DebuggerStepThrough()>
    Private Sub InitializeComponent()
        Me.Panel1 = New System.Windows.Forms.Panel()
        Me.btn_logout = New System.Windows.Forms.Button()
        Me.Panel2 = New System.Windows.Forms.Panel()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.GroupBoxPengajuan = New System.Windows.Forms.GroupBox()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.lbl_eskul = New System.Windows.Forms.Label()
        Me.LabelFilter = New System.Windows.Forms.Label()
        Me.cb_filterstatus = New System.Windows.Forms.ComboBox()
        Me.lbl_nama = New System.Windows.Forms.Label()
        Me.btn_batalkan = New System.Windows.Forms.Button()
        Me.LabelJudul = New System.Windows.Forms.Label()
        Me.txt_judulevent = New System.Windows.Forms.TextBox()
        Me.LabelJenis = New System.Windows.Forms.Label()
        Me.txt_jeniskegiatan = New System.Windows.Forms.TextBox()
        Me.LabelTotal = New System.Windows.Forms.Label()
        Me.txt_totalpembiayaan = New System.Windows.Forms.TextBox()
        Me.LabelProposal = New System.Windows.Forms.Label()
        Me.txt_proposal = New System.Windows.Forms.TextBox()
        Me.LabelDeskripsi = New System.Windows.Forms.Label()
        Me.txt_deskripsi = New System.Windows.Forms.TextBox()
        Me.LabelTanggal = New System.Windows.Forms.Label()
        Me.date_tglpengajuan = New System.Windows.Forms.DateTimePicker()
        Me.btn_kirim = New System.Windows.Forms.Button()
        Me.dgv_dashboard = New System.Windows.Forms.DataGridView()
        Me.Panel1.SuspendLayout()
        Me.Panel2.SuspendLayout()
        Me.GroupBoxPengajuan.SuspendLayout()
        CType(Me.dgv_dashboard, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'Panel1
        '
        Me.Panel1.BackColor = System.Drawing.Color.Navy
        Me.Panel1.Controls.Add(Me.btn_logout)
        Me.Panel1.Dock = System.Windows.Forms.DockStyle.Left
        Me.Panel1.Location = New System.Drawing.Point(0, 110)
        Me.Panel1.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.Panel1.Name = "Panel1"
        Me.Panel1.Size = New System.Drawing.Size(170, 780)
        Me.Panel1.TabIndex = 0
        '
        'btn_logout
        '
        Me.btn_logout.Location = New System.Drawing.Point(26, 711)
        Me.btn_logout.Name = "btn_logout"
        Me.btn_logout.Size = New System.Drawing.Size(112, 37)
        Me.btn_logout.TabIndex = 21
        Me.btn_logout.Text = "Logout"
        Me.btn_logout.UseVisualStyleBackColor = True
        '
        'Panel2
        '
        Me.Panel2.BackColor = System.Drawing.Color.Navy
        Me.Panel2.Controls.Add(Me.Label1)
        Me.Panel2.Dock = System.Windows.Forms.DockStyle.Top
        Me.Panel2.Location = New System.Drawing.Point(0, 0)
        Me.Panel2.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(1280, 110)
        Me.Panel2.TabIndex = 1
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Poppins Medium", 13.8!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.ForeColor = System.Drawing.Color.White
        Me.Label1.Location = New System.Drawing.Point(420, 35)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(440, 40)
        Me.Label1.TabIndex = 1
        Me.Label1.Text = "Aplikasi Pengajuan Proposal V 1.0"
        '
        'GroupBoxPengajuan
        '
        Me.GroupBoxPengajuan.Controls.Add(Me.Label2)
        Me.GroupBoxPengajuan.Controls.Add(Me.Label3)
        Me.GroupBoxPengajuan.Controls.Add(Me.lbl_eskul)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelFilter)
        Me.GroupBoxPengajuan.Controls.Add(Me.cb_filterstatus)
        Me.GroupBoxPengajuan.Controls.Add(Me.lbl_nama)
        Me.GroupBoxPengajuan.Controls.Add(Me.btn_batalkan)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelJudul)
        Me.GroupBoxPengajuan.Controls.Add(Me.txt_judulevent)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelJenis)
        Me.GroupBoxPengajuan.Controls.Add(Me.txt_jeniskegiatan)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelTotal)
        Me.GroupBoxPengajuan.Controls.Add(Me.txt_totalpembiayaan)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelProposal)
        Me.GroupBoxPengajuan.Controls.Add(Me.txt_proposal)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelDeskripsi)
        Me.GroupBoxPengajuan.Controls.Add(Me.txt_deskripsi)
        Me.GroupBoxPengajuan.Controls.Add(Me.LabelTanggal)
        Me.GroupBoxPengajuan.Controls.Add(Me.date_tglpengajuan)
        Me.GroupBoxPengajuan.Controls.Add(Me.btn_kirim)
        Me.GroupBoxPengajuan.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.GroupBoxPengajuan.Location = New System.Drawing.Point(255, 142)
        Me.GroupBoxPengajuan.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.GroupBoxPengajuan.Name = "GroupBoxPengajuan"
        Me.GroupBoxPengajuan.Padding = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.GroupBoxPengajuan.Size = New System.Drawing.Size(962, 367)
        Me.GroupBoxPengajuan.TabIndex = 2
        Me.GroupBoxPengajuan.TabStop = False
        Me.GroupBoxPengajuan.Text = "Pengajuan Event"
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Location = New System.Drawing.Point(474, 120)
        Me.Label2.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(64, 30)
        Me.Label2.TabIndex = 36
        Me.Label2.Text = "Ekskul"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.Location = New System.Drawing.Point(474, 62)
        Me.Label3.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(157, 30)
        Me.Label3.TabIndex = 35
        Me.Label3.Text = "Nama Pengguna"
        '
        'lbl_eskul
        '
        Me.lbl_eskul.AutoSize = True
        Me.lbl_eskul.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.lbl_eskul.Location = New System.Drawing.Point(688, 114)
        Me.lbl_eskul.Name = "lbl_eskul"
        Me.lbl_eskul.Size = New System.Drawing.Size(22, 30)
        Me.lbl_eskul.TabIndex = 34
        Me.lbl_eskul.Text = "-"
        '
        'LabelFilter
        '
        Me.LabelFilter.AutoSize = True
        Me.LabelFilter.Location = New System.Drawing.Point(474, 238)
        Me.LabelFilter.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelFilter.Name = "LabelFilter"
        Me.LabelFilter.Size = New System.Drawing.Size(111, 30)
        Me.LabelFilter.TabIndex = 13
        Me.LabelFilter.Text = "Filter Status"
        '
        'cb_filterstatus
        '
        Me.cb_filterstatus.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList
        Me.cb_filterstatus.Location = New System.Drawing.Point(479, 290)
        Me.cb_filterstatus.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.cb_filterstatus.Name = "cb_filterstatus"
        Me.cb_filterstatus.Size = New System.Drawing.Size(180, 38)
        Me.cb_filterstatus.TabIndex = 14
        '
        'lbl_nama
        '
        Me.lbl_nama.AutoSize = True
        Me.lbl_nama.Font = New System.Drawing.Font("Poppins", 10.0!)
        Me.lbl_nama.Location = New System.Drawing.Point(688, 65)
        Me.lbl_nama.Name = "lbl_nama"
        Me.lbl_nama.Size = New System.Drawing.Size(22, 30)
        Me.lbl_nama.TabIndex = 33
        Me.lbl_nama.Text = "-"
        '
        'btn_batalkan
        '
        Me.btn_batalkan.Location = New System.Drawing.Point(693, 291)
        Me.btn_batalkan.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.btn_batalkan.Name = "btn_batalkan"
        Me.btn_batalkan.Size = New System.Drawing.Size(180, 34)
        Me.btn_batalkan.TabIndex = 15
        Me.btn_batalkan.Text = "Batalkan"
        Me.btn_batalkan.UseVisualStyleBackColor = True
        '
        'LabelJudul
        '
        Me.LabelJudul.AutoSize = True
        Me.LabelJudul.Location = New System.Drawing.Point(21, 65)
        Me.LabelJudul.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelJudul.Name = "LabelJudul"
        Me.LabelJudul.Size = New System.Drawing.Size(111, 30)
        Me.LabelJudul.TabIndex = 0
        Me.LabelJudul.Text = "Judul Event"
        '
        'txt_judulevent
        '
        Me.txt_judulevent.Location = New System.Drawing.Point(259, 62)
        Me.txt_judulevent.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.txt_judulevent.Name = "txt_judulevent"
        Me.txt_judulevent.Size = New System.Drawing.Size(180, 32)
        Me.txt_judulevent.TabIndex = 1
        '
        'LabelJenis
        '
        Me.LabelJenis.AutoSize = True
        Me.LabelJenis.Location = New System.Drawing.Point(21, 119)
        Me.LabelJenis.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelJenis.Name = "LabelJenis"
        Me.LabelJenis.Size = New System.Drawing.Size(137, 30)
        Me.LabelJenis.TabIndex = 2
        Me.LabelJenis.Text = "Jenis Kegiatan"
        '
        'txt_jeniskegiatan
        '
        Me.txt_jeniskegiatan.Location = New System.Drawing.Point(259, 121)
        Me.txt_jeniskegiatan.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.txt_jeniskegiatan.Name = "txt_jeniskegiatan"
        Me.txt_jeniskegiatan.Size = New System.Drawing.Size(180, 32)
        Me.txt_jeniskegiatan.TabIndex = 3
        '
        'LabelTotal
        '
        Me.LabelTotal.AutoSize = True
        Me.LabelTotal.Location = New System.Drawing.Point(21, 180)
        Me.LabelTotal.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelTotal.Name = "LabelTotal"
        Me.LabelTotal.Size = New System.Drawing.Size(167, 30)
        Me.LabelTotal.TabIndex = 4
        Me.LabelTotal.Text = "Total Pembiayaan"
        '
        'txt_totalpembiayaan
        '
        Me.txt_totalpembiayaan.Location = New System.Drawing.Point(259, 177)
        Me.txt_totalpembiayaan.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.txt_totalpembiayaan.Name = "txt_totalpembiayaan"
        Me.txt_totalpembiayaan.Size = New System.Drawing.Size(180, 32)
        Me.txt_totalpembiayaan.TabIndex = 5
        '
        'LabelProposal
        '
        Me.LabelProposal.AutoSize = True
        Me.LabelProposal.Location = New System.Drawing.Point(21, 236)
        Me.LabelProposal.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelProposal.Name = "LabelProposal"
        Me.LabelProposal.Size = New System.Drawing.Size(86, 30)
        Me.LabelProposal.TabIndex = 6
        Me.LabelProposal.Text = "Proposal"
        '
        'txt_proposal
        '
        Me.txt_proposal.Location = New System.Drawing.Point(259, 233)
        Me.txt_proposal.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.txt_proposal.Name = "txt_proposal"
        Me.txt_proposal.Size = New System.Drawing.Size(180, 32)
        Me.txt_proposal.TabIndex = 7
        '
        'LabelDeskripsi
        '
        Me.LabelDeskripsi.AutoSize = True
        Me.LabelDeskripsi.Location = New System.Drawing.Point(21, 304)
        Me.LabelDeskripsi.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelDeskripsi.Name = "LabelDeskripsi"
        Me.LabelDeskripsi.Size = New System.Drawing.Size(88, 30)
        Me.LabelDeskripsi.TabIndex = 8
        Me.LabelDeskripsi.Text = "Deskripsi"
        '
        'txt_deskripsi
        '
        Me.txt_deskripsi.Location = New System.Drawing.Point(259, 290)
        Me.txt_deskripsi.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.txt_deskripsi.Multiline = True
        Me.txt_deskripsi.Name = "txt_deskripsi"
        Me.txt_deskripsi.Size = New System.Drawing.Size(180, 60)
        Me.txt_deskripsi.TabIndex = 9
        '
        'LabelTanggal
        '
        Me.LabelTanggal.AutoSize = True
        Me.LabelTanggal.Location = New System.Drawing.Point(474, 180)
        Me.LabelTanggal.Margin = New System.Windows.Forms.Padding(2, 0, 2, 0)
        Me.LabelTanggal.Name = "LabelTanggal"
        Me.LabelTanggal.Size = New System.Drawing.Size(177, 30)
        Me.LabelTanggal.TabIndex = 10
        Me.LabelTanggal.Text = "Tanggal Pengajuan"
        '
        'date_tglpengajuan
        '
        Me.date_tglpengajuan.Format = System.Windows.Forms.DateTimePickerFormat.[Short]
        Me.date_tglpengajuan.Location = New System.Drawing.Point(693, 177)
        Me.date_tglpengajuan.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.date_tglpengajuan.Name = "date_tglpengajuan"
        Me.date_tglpengajuan.Size = New System.Drawing.Size(180, 32)
        Me.date_tglpengajuan.TabIndex = 11
        '
        'btn_kirim
        '
        Me.btn_kirim.Location = New System.Drawing.Point(693, 233)
        Me.btn_kirim.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.btn_kirim.Name = "btn_kirim"
        Me.btn_kirim.Size = New System.Drawing.Size(180, 36)
        Me.btn_kirim.TabIndex = 12
        Me.btn_kirim.Text = "Kirim"
        Me.btn_kirim.UseVisualStyleBackColor = True
        '
        'dgv_dashboard
        '
        Me.dgv_dashboard.AllowUserToAddRows = False
        Me.dgv_dashboard.AllowUserToDeleteRows = False
        Me.dgv_dashboard.BackgroundColor = System.Drawing.Color.Gray
        Me.dgv_dashboard.ColumnHeadersHeight = 29
        Me.dgv_dashboard.Location = New System.Drawing.Point(255, 554)
        Me.dgv_dashboard.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.dgv_dashboard.Name = "dgv_dashboard"
        Me.dgv_dashboard.ReadOnly = True
        Me.dgv_dashboard.RowHeadersWidth = 51
        Me.dgv_dashboard.SelectionMode = System.Windows.Forms.DataGridViewSelectionMode.FullRowSelect
        Me.dgv_dashboard.Size = New System.Drawing.Size(962, 304)
        Me.dgv_dashboard.TabIndex = 3
        '
        'FormUser
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(10.0!, 30.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.Color.White
        Me.ClientSize = New System.Drawing.Size(1280, 890)
        Me.Controls.Add(Me.Panel1)
        Me.Controls.Add(Me.Panel2)
        Me.Controls.Add(Me.GroupBoxPengajuan)
        Me.Controls.Add(Me.dgv_dashboard)
        Me.Font = New System.Drawing.Font("Poppins", 10.2!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None
        Me.Margin = New System.Windows.Forms.Padding(2, 4, 2, 4)
        Me.Name = "FormUser"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = " "
        Me.Panel1.ResumeLayout(False)
        Me.Panel2.ResumeLayout(False)
        Me.Panel2.PerformLayout()
        Me.GroupBoxPengajuan.ResumeLayout(False)
        Me.GroupBoxPengajuan.PerformLayout()
        CType(Me.dgv_dashboard, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    ' Deklarasi variabel kontrol di sini, BUKAN di dalam Sub!
    Friend WithEvents Panel1 As Panel
    Friend WithEvents Panel2 As Panel
    Friend WithEvents GroupBoxPengajuan As GroupBox
    Friend WithEvents txt_judulevent As TextBox
    Friend WithEvents txt_jeniskegiatan As TextBox
    Friend WithEvents txt_totalpembiayaan As TextBox
    Friend WithEvents txt_proposal As TextBox
    Friend WithEvents txt_deskripsi As TextBox
    Friend WithEvents date_tglpengajuan As DateTimePicker
    Friend WithEvents btn_kirim As Button
    Friend WithEvents LabelJudul As Label
    Friend WithEvents LabelJenis As Label
    Friend WithEvents LabelTotal As Label
    Friend WithEvents LabelProposal As Label
    Friend WithEvents LabelDeskripsi As Label
    Friend WithEvents LabelTanggal As Label
    Friend WithEvents dgv_dashboard As DataGridView
    Friend WithEvents Label1 As Label
    Friend WithEvents LabelFilter As Label
    Friend WithEvents cb_filterstatus As ComboBox
    Friend WithEvents btn_batalkan As Button
    Friend WithEvents Label2 As Label
    Friend WithEvents Label3 As Label
    Friend WithEvents btn_logout As Button
    Friend WithEvents lbl_eskul As Label
    Friend WithEvents lbl_nama As Label
End Class
