<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FormDashboardAdmin
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
        Me.btnReject = New System.Windows.Forms.Button()
        Me.btnApprove = New System.Windows.Forms.Button()
        Me.dgvEventAdmin = New System.Windows.Forms.DataGridView()
        Me.PanelSidebar = New System.Windows.Forms.Panel()
        Me.btnLaporan = New System.Windows.Forms.Button()
        Me.btnGantiPassword = New System.Windows.Forms.Button()
        Me.btnDashboard = New System.Windows.Forms.Button()
        Me.btnHakAkses = New System.Windows.Forms.Button()
        Me.Button1 = New System.Windows.Forms.Button()
        Me.lblrole = New System.Windows.Forms.Label()
        Me.btnLogout = New System.Windows.Forms.Button()
        Me.lblnama = New System.Windows.Forms.Label()
        Me.btnDelete = New System.Windows.Forms.Button()
        Me.Panel2.SuspendLayout()
        CType(Me.dgvEventAdmin, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.PanelSidebar.SuspendLayout()
        Me.SuspendLayout()
        '
        'Panel2
        '
        Me.Panel2.Controls.Add(Me.btnDelete)
        Me.Panel2.Controls.Add(Me.btnReject)
        Me.Panel2.Controls.Add(Me.btnApprove)
        Me.Panel2.Controls.Add(Me.dgvEventAdmin)
        Me.Panel2.Controls.Add(Me.PanelSidebar)
        Me.Panel2.Dock = System.Windows.Forms.DockStyle.Fill
        Me.Panel2.Location = New System.Drawing.Point(0, 0)
        Me.Panel2.Name = "Panel2"
        Me.Panel2.Size = New System.Drawing.Size(1451, 771)
        Me.Panel2.TabIndex = 2
        '
        'btnReject
        '
        Me.btnReject.BackColor = System.Drawing.Color.Red
        Me.btnReject.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnReject.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnReject.Location = New System.Drawing.Point(1206, 541)
        Me.btnReject.Name = "btnReject"
        Me.btnReject.Size = New System.Drawing.Size(188, 68)
        Me.btnReject.TabIndex = 4
        Me.btnReject.Text = "Tolak"
        Me.btnReject.UseVisualStyleBackColor = False
        '
        'btnApprove
        '
        Me.btnApprove.AutoSize = True
        Me.btnApprove.BackColor = System.Drawing.Color.Green
        Me.btnApprove.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnApprove.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnApprove.Location = New System.Drawing.Point(926, 541)
        Me.btnApprove.Name = "btnApprove"
        Me.btnApprove.Size = New System.Drawing.Size(188, 68)
        Me.btnApprove.TabIndex = 3
        Me.btnApprove.Text = "Setuju"
        Me.btnApprove.UseVisualStyleBackColor = False
        '
        'dgvEventAdmin
        '
        Me.dgvEventAdmin.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvEventAdmin.Location = New System.Drawing.Point(329, 45)
        Me.dgvEventAdmin.Name = "dgvEventAdmin"
        Me.dgvEventAdmin.RowHeadersBorderStyle = System.Windows.Forms.DataGridViewHeaderBorderStyle.None
        Me.dgvEventAdmin.RowHeadersWidth = 82
        Me.dgvEventAdmin.RowTemplate.Height = 33
        Me.dgvEventAdmin.Size = New System.Drawing.Size(1065, 462)
        Me.dgvEventAdmin.TabIndex = 2
        '
        'PanelSidebar
        '
        Me.PanelSidebar.BackColor = System.Drawing.SystemColors.Highlight
        Me.PanelSidebar.Controls.Add(Me.btnLaporan)
        Me.PanelSidebar.Controls.Add(Me.btnGantiPassword)
        Me.PanelSidebar.Controls.Add(Me.btnDashboard)
        Me.PanelSidebar.Controls.Add(Me.btnHakAkses)
        Me.PanelSidebar.Controls.Add(Me.Button1)
        Me.PanelSidebar.Controls.Add(Me.lblrole)
        Me.PanelSidebar.Controls.Add(Me.btnLogout)
        Me.PanelSidebar.Controls.Add(Me.lblnama)
        Me.PanelSidebar.Dock = System.Windows.Forms.DockStyle.Left
        Me.PanelSidebar.Location = New System.Drawing.Point(0, 0)
        Me.PanelSidebar.Name = "PanelSidebar"
        Me.PanelSidebar.Size = New System.Drawing.Size(297, 771)
        Me.PanelSidebar.TabIndex = 1
        '
        'btnLaporan
        '
        Me.btnLaporan.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnLaporan.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnLaporan.Location = New System.Drawing.Point(60, 344)
        Me.btnLaporan.Name = "btnLaporan"
        Me.btnLaporan.Size = New System.Drawing.Size(168, 75)
        Me.btnLaporan.TabIndex = 8
        Me.btnLaporan.Text = "Laporan"
        Me.btnLaporan.UseVisualStyleBackColor = True
        '
        'btnGantiPassword
        '
        Me.btnGantiPassword.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnGantiPassword.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnGantiPassword.Location = New System.Drawing.Point(60, 448)
        Me.btnGantiPassword.Name = "btnGantiPassword"
        Me.btnGantiPassword.Size = New System.Drawing.Size(168, 75)
        Me.btnGantiPassword.TabIndex = 7
        Me.btnGantiPassword.Text = "Profile"
        Me.btnGantiPassword.UseVisualStyleBackColor = True
        '
        'btnDashboard
        '
        Me.btnDashboard.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnDashboard.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnDashboard.Location = New System.Drawing.Point(60, 179)
        Me.btnDashboard.Name = "btnDashboard"
        Me.btnDashboard.Size = New System.Drawing.Size(168, 54)
        Me.btnDashboard.TabIndex = 6
        Me.btnDashboard.Text = "Dashboard"
        Me.btnDashboard.UseVisualStyleBackColor = True
        '
        'btnHakAkses
        '
        Me.btnHakAkses.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnHakAkses.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnHakAkses.Location = New System.Drawing.Point(60, 261)
        Me.btnHakAkses.Name = "btnHakAkses"
        Me.btnHakAkses.Size = New System.Drawing.Size(168, 54)
        Me.btnHakAkses.TabIndex = 5
        Me.btnHakAkses.Text = "Hak Akses"
        Me.btnHakAkses.UseVisualStyleBackColor = True
        '
        'Button1
        '
        Me.Button1.BackColor = System.Drawing.Color.Red
        Me.Button1.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.Button1.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Button1.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.Button1.Location = New System.Drawing.Point(60, 670)
        Me.Button1.Name = "Button1"
        Me.Button1.Size = New System.Drawing.Size(168, 52)
        Me.Button1.TabIndex = 4
        Me.Button1.Text = "Logout"
        Me.Button1.UseVisualStyleBackColor = False
        '
        'lblrole
        '
        Me.lblrole.AutoSize = True
        Me.lblrole.BackColor = System.Drawing.SystemColors.Highlight
        Me.lblrole.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblrole.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.lblrole.Location = New System.Drawing.Point(107, 109)
        Me.lblrole.Name = "lblrole"
        Me.lblrole.Size = New System.Drawing.Size(74, 31)
        Me.lblrole.TabIndex = 4
        Me.lblrole.Text = "Role"
        '
        'btnLogout
        '
        Me.btnLogout.BackColor = System.Drawing.Color.Red
        Me.btnLogout.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnLogout.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnLogout.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnLogout.Location = New System.Drawing.Point(60, 846)
        Me.btnLogout.Name = "btnLogout"
        Me.btnLogout.Size = New System.Drawing.Size(168, 52)
        Me.btnLogout.TabIndex = 3
        Me.btnLogout.Text = "Logout"
        Me.btnLogout.UseVisualStyleBackColor = False
        '
        'lblnama
        '
        Me.lblnama.AutoSize = True
        Me.lblnama.BackColor = System.Drawing.SystemColors.Highlight
        Me.lblnama.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblnama.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.lblnama.Location = New System.Drawing.Point(28, 54)
        Me.lblnama.Name = "lblnama"
        Me.lblnama.Size = New System.Drawing.Size(118, 25)
        Me.lblnama.TabIndex = 0
        Me.lblnama.Text = "Username"
        '
        'btnDelete
        '
        Me.btnDelete.AutoSize = True
        Me.btnDelete.BackColor = System.Drawing.SystemColors.ActiveCaption
        Me.btnDelete.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnDelete.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnDelete.Location = New System.Drawing.Point(651, 541)
        Me.btnDelete.Name = "btnDelete"
        Me.btnDelete.Size = New System.Drawing.Size(188, 68)
        Me.btnDelete.TabIndex = 5
        Me.btnDelete.Text = "Hapus"
        Me.btnDelete.UseVisualStyleBackColor = False
        '
        'FormDashboardAdmin
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(12.0!, 25.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.AutoSize = True
        Me.BackColor = System.Drawing.SystemColors.ButtonHighlight
        Me.ClientSize = New System.Drawing.Size(1451, 771)
        Me.Controls.Add(Me.Panel2)
        Me.MaximizeBox = False
        Me.MinimizeBox = False
        Me.Name = "FormDashboardAdmin"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "FormDashboardAdmin"
        Me.Panel2.ResumeLayout(False)
        Me.Panel2.PerformLayout()
        CType(Me.dgvEventAdmin, System.ComponentModel.ISupportInitialize).EndInit()
        Me.PanelSidebar.ResumeLayout(False)
        Me.PanelSidebar.PerformLayout()
        Me.ResumeLayout(False)

    End Sub
    Friend WithEvents Panel2 As Panel
    Friend WithEvents PanelSidebar As Panel
    Friend WithEvents lblrole As Label
    Friend WithEvents btnLogout As Button
    Friend WithEvents lblnama As Label
    Friend WithEvents Button1 As Button
    Friend WithEvents btnHakAkses As Button
    Friend WithEvents btnDashboard As Button
    Friend WithEvents dgvEventAdmin As DataGridView
    Friend WithEvents btnReject As Button
    Friend WithEvents btnApprove As Button
    Friend WithEvents btnGantiPassword As Button
    Friend WithEvents btnLaporan As Button
    Friend WithEvents btnDelete As Button
End Class
