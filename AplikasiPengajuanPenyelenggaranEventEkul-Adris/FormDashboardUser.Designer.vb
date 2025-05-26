<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()>
Partial Class FormDashboardUser
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
        Me.PanelSidebar = New System.Windows.Forms.Panel()
        Me.btnGantipassword = New System.Windows.Forms.Button()
        Me.lblrole = New System.Windows.Forms.Label()
        Me.btnLogout = New System.Windows.Forms.Button()
        Me.lblnama = New System.Windows.Forms.Label()
        Me.btnAjukanEvent = New System.Windows.Forms.Button()
        Me.btnDashboard = New System.Windows.Forms.Button()
        Me.PanelKonten = New System.Windows.Forms.Panel()
        Me.dgvRekapEvent = New System.Windows.Forms.DataGridView()
        Me.Label1 = New System.Windows.Forms.Label()
        Me.PanelSidebar.SuspendLayout()
        Me.PanelKonten.SuspendLayout()
        CType(Me.dgvRekapEvent, System.ComponentModel.ISupportInitialize).BeginInit()
        Me.SuspendLayout()
        '
        'PanelSidebar
        '
        Me.PanelSidebar.BackColor = System.Drawing.SystemColors.Highlight
        Me.PanelSidebar.Controls.Add(Me.btnGantipassword)
        Me.PanelSidebar.Controls.Add(Me.lblrole)
        Me.PanelSidebar.Controls.Add(Me.btnLogout)
        Me.PanelSidebar.Controls.Add(Me.lblnama)
        Me.PanelSidebar.Controls.Add(Me.btnAjukanEvent)
        Me.PanelSidebar.Controls.Add(Me.btnDashboard)
        Me.PanelSidebar.Dock = System.Windows.Forms.DockStyle.Left
        Me.PanelSidebar.Location = New System.Drawing.Point(0, 0)
        Me.PanelSidebar.Name = "PanelSidebar"
        Me.PanelSidebar.Size = New System.Drawing.Size(297, 674)
        Me.PanelSidebar.TabIndex = 0
        '
        'btnGantipassword
        '
        Me.btnGantipassword.BackColor = System.Drawing.Color.Transparent
        Me.btnGantipassword.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnGantipassword.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnGantipassword.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnGantipassword.Location = New System.Drawing.Point(60, 362)
        Me.btnGantipassword.Name = "btnGantipassword"
        Me.btnGantipassword.Size = New System.Drawing.Size(194, 69)
        Me.btnGantipassword.TabIndex = 5
        Me.btnGantipassword.Text = "Profile"
        Me.btnGantipassword.UseVisualStyleBackColor = False
        '
        'lblrole
        '
        Me.lblrole.AutoSize = True
        Me.lblrole.BackColor = System.Drawing.SystemColors.Highlight
        Me.lblrole.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblrole.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.lblrole.Location = New System.Drawing.Point(104, 107)
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
        Me.btnLogout.Location = New System.Drawing.Point(60, 548)
        Me.btnLogout.Name = "btnLogout"
        Me.btnLogout.Size = New System.Drawing.Size(194, 52)
        Me.btnLogout.TabIndex = 3
        Me.btnLogout.Text = "Logout"
        Me.btnLogout.UseVisualStyleBackColor = False
        '
        'lblnama
        '
        Me.lblnama.AutoSize = True
        Me.lblnama.BackColor = System.Drawing.SystemColors.Highlight
        Me.lblnama.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblnama.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.lblnama.Location = New System.Drawing.Point(28, 54)
        Me.lblnama.Name = "lblnama"
        Me.lblnama.Size = New System.Drawing.Size(110, 25)
        Me.lblnama.TabIndex = 0
        Me.lblnama.Text = "Username"
        '
        'btnAjukanEvent
        '
        Me.btnAjukanEvent.BackColor = System.Drawing.Color.Transparent
        Me.btnAjukanEvent.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnAjukanEvent.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnAjukanEvent.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnAjukanEvent.Location = New System.Drawing.Point(60, 279)
        Me.btnAjukanEvent.Name = "btnAjukanEvent"
        Me.btnAjukanEvent.Size = New System.Drawing.Size(194, 52)
        Me.btnAjukanEvent.TabIndex = 1
        Me.btnAjukanEvent.Text = "Ajukan Event"
        Me.btnAjukanEvent.UseVisualStyleBackColor = False
        '
        'btnDashboard
        '
        Me.btnDashboard.BackColor = System.Drawing.Color.Transparent
        Me.btnDashboard.FlatStyle = System.Windows.Forms.FlatStyle.Flat
        Me.btnDashboard.Font = New System.Drawing.Font("Microsoft Sans Serif", 7.875!, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnDashboard.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnDashboard.Location = New System.Drawing.Point(60, 191)
        Me.btnDashboard.Name = "btnDashboard"
        Me.btnDashboard.Size = New System.Drawing.Size(194, 52)
        Me.btnDashboard.TabIndex = 0
        Me.btnDashboard.Text = "Dashboard"
        Me.btnDashboard.UseVisualStyleBackColor = False
        '
        'PanelKonten
        '
        Me.PanelKonten.BackColor = System.Drawing.Color.Transparent
        Me.PanelKonten.Controls.Add(Me.dgvRekapEvent)
        Me.PanelKonten.Controls.Add(Me.Label1)
        Me.PanelKonten.Dock = System.Windows.Forms.DockStyle.Fill
        Me.PanelKonten.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.PanelKonten.Location = New System.Drawing.Point(297, 0)
        Me.PanelKonten.Name = "PanelKonten"
        Me.PanelKonten.Size = New System.Drawing.Size(933, 674)
        Me.PanelKonten.TabIndex = 1
        '
        'dgvRekapEvent
        '
        Me.dgvRekapEvent.Anchor = CType((((System.Windows.Forms.AnchorStyles.Top Or System.Windows.Forms.AnchorStyles.Bottom) _
            Or System.Windows.Forms.AnchorStyles.Left) _
            Or System.Windows.Forms.AnchorStyles.Right), System.Windows.Forms.AnchorStyles)
        Me.dgvRekapEvent.ColumnHeadersHeightSizeMode = System.Windows.Forms.DataGridViewColumnHeadersHeightSizeMode.AutoSize
        Me.dgvRekapEvent.Location = New System.Drawing.Point(71, 54)
        Me.dgvRekapEvent.Name = "dgvRekapEvent"
        Me.dgvRekapEvent.RowHeadersWidth = 82
        Me.dgvRekapEvent.RowTemplate.Height = 33
        Me.dgvRekapEvent.Size = New System.Drawing.Size(822, 546)
        Me.dgvRekapEvent.TabIndex = 2
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Location = New System.Drawing.Point(122, 142)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(0, 25)
        Me.Label1.TabIndex = 1
        '
        'FormDashboardUser
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(12.0!, 25.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.SystemColors.ButtonHighlight
        Me.ClientSize = New System.Drawing.Size(1230, 674)
        Me.Controls.Add(Me.PanelKonten)
        Me.Controls.Add(Me.PanelSidebar)
        Me.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle
        Me.MinimizeBox = False
        Me.Name = "FormDashboardUser"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "FormDashboardUser"
        Me.PanelSidebar.ResumeLayout(False)
        Me.PanelSidebar.PerformLayout()
        Me.PanelKonten.ResumeLayout(False)
        Me.PanelKonten.PerformLayout()
        CType(Me.dgvRekapEvent, System.ComponentModel.ISupportInitialize).EndInit()
        Me.ResumeLayout(False)

    End Sub

    Friend WithEvents PanelSidebar As Panel
    Friend WithEvents btnLogout As Button
    Friend WithEvents btnAjukanEvent As Button
    Friend WithEvents PanelKonten As Panel
    Friend WithEvents lblnama As Label
    Friend WithEvents Label1 As Label
    Friend WithEvents dgvRekapEvent As DataGridView
    Friend WithEvents lblrole As Label
    Friend WithEvents btnGantipassword As Button
    Friend WithEvents btnDashboard As Button
End Class
