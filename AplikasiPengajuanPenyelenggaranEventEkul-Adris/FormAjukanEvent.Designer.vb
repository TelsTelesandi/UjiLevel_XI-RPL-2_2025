<Global.Microsoft.VisualBasic.CompilerServices.DesignerGenerated()> _
Partial Class FormAjukanEvent
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
        Me.Label1 = New System.Windows.Forms.Label()
        Me.Label2 = New System.Windows.Forms.Label()
        Me.Label3 = New System.Windows.Forms.Label()
        Me.Label4 = New System.Windows.Forms.Label()
        Me.lblFilePath = New System.Windows.Forms.Label()
        Me.txtJudulEvent = New System.Windows.Forms.TextBox()
        Me.txtJudulKegiatan = New System.Windows.Forms.TextBox()
        Me.txtPembiayaan = New System.Windows.Forms.TextBox()
        Me.txtDeskripsi = New System.Windows.Forms.TextBox()
        Me.btnBrowse = New System.Windows.Forms.Button()
        Me.btnAjukanEvent = New System.Windows.Forms.Button()
        Me.Label6 = New System.Windows.Forms.Label()
        Me.Button1 = New System.Windows.Forms.Button()
        Me.SuspendLayout()
        '
        'Label1
        '
        Me.Label1.AutoSize = True
        Me.Label1.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label1.Location = New System.Drawing.Point(115, 99)
        Me.Label1.Name = "Label1"
        Me.Label1.Size = New System.Drawing.Size(156, 31)
        Me.Label1.TabIndex = 0
        Me.Label1.Text = "Judul Event"
        '
        'Label2
        '
        Me.Label2.AutoSize = True
        Me.Label2.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label2.Location = New System.Drawing.Point(115, 173)
        Me.Label2.Name = "Label2"
        Me.Label2.Size = New System.Drawing.Size(192, 31)
        Me.Label2.TabIndex = 1
        Me.Label2.Text = "Jenis Kegiatan"
        '
        'Label3
        '
        Me.Label3.AutoSize = True
        Me.Label3.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label3.Location = New System.Drawing.Point(115, 252)
        Me.Label3.Name = "Label3"
        Me.Label3.Size = New System.Drawing.Size(232, 31)
        Me.Label3.TabIndex = 2
        Me.Label3.Text = "Total Pembiayaan"
        '
        'Label4
        '
        Me.Label4.AutoSize = True
        Me.Label4.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label4.Location = New System.Drawing.Point(115, 317)
        Me.Label4.Name = "Label4"
        Me.Label4.Size = New System.Drawing.Size(127, 31)
        Me.Label4.TabIndex = 3
        Me.Label4.Text = "Deskripsi"
        '
        'lblFilePath
        '
        Me.lblFilePath.AutoSize = True
        Me.lblFilePath.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.lblFilePath.Location = New System.Drawing.Point(359, 473)
        Me.lblFilePath.Name = "lblFilePath"
        Me.lblFilePath.Size = New System.Drawing.Size(269, 31)
        Me.lblFilePath.TabIndex = 4
        Me.lblFilePath.Text = "Tampilkan Nama File"
        '
        'txtJudulEvent
        '
        Me.txtJudulEvent.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtJudulEvent.Location = New System.Drawing.Point(365, 99)
        Me.txtJudulEvent.Multiline = True
        Me.txtJudulEvent.Name = "txtJudulEvent"
        Me.txtJudulEvent.Size = New System.Drawing.Size(541, 40)
        Me.txtJudulEvent.TabIndex = 5
        '
        'txtJudulKegiatan
        '
        Me.txtJudulKegiatan.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtJudulKegiatan.Location = New System.Drawing.Point(365, 173)
        Me.txtJudulKegiatan.Multiline = True
        Me.txtJudulKegiatan.Name = "txtJudulKegiatan"
        Me.txtJudulKegiatan.Size = New System.Drawing.Size(541, 40)
        Me.txtJudulKegiatan.TabIndex = 6
        '
        'txtPembiayaan
        '
        Me.txtPembiayaan.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtPembiayaan.Location = New System.Drawing.Point(365, 252)
        Me.txtPembiayaan.Multiline = True
        Me.txtPembiayaan.Name = "txtPembiayaan"
        Me.txtPembiayaan.Size = New System.Drawing.Size(541, 40)
        Me.txtPembiayaan.TabIndex = 7
        '
        'txtDeskripsi
        '
        Me.txtDeskripsi.BorderStyle = System.Windows.Forms.BorderStyle.FixedSingle
        Me.txtDeskripsi.Location = New System.Drawing.Point(365, 317)
        Me.txtDeskripsi.Multiline = True
        Me.txtDeskripsi.Name = "txtDeskripsi"
        Me.txtDeskripsi.Size = New System.Drawing.Size(541, 40)
        Me.txtDeskripsi.TabIndex = 8
        '
        'btnBrowse
        '
        Me.btnBrowse.BackColor = System.Drawing.Color.DodgerBlue
        Me.btnBrowse.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnBrowse.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnBrowse.Location = New System.Drawing.Point(663, 398)
        Me.btnBrowse.Name = "btnBrowse"
        Me.btnBrowse.Size = New System.Drawing.Size(243, 44)
        Me.btnBrowse.TabIndex = 9
        Me.btnBrowse.Text = "Browse"
        Me.btnBrowse.UseVisualStyleBackColor = False
        '
        'btnAjukanEvent
        '
        Me.btnAjukanEvent.BackColor = System.Drawing.Color.Blue
        Me.btnAjukanEvent.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.btnAjukanEvent.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.btnAjukanEvent.Location = New System.Drawing.Point(365, 543)
        Me.btnAjukanEvent.Name = "btnAjukanEvent"
        Me.btnAjukanEvent.Size = New System.Drawing.Size(357, 58)
        Me.btnAjukanEvent.TabIndex = 10
        Me.btnAjukanEvent.Text = "Ajukan Event"
        Me.btnAjukanEvent.UseVisualStyleBackColor = False
        '
        'Label6
        '
        Me.Label6.AutoSize = True
        Me.Label6.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Label6.Location = New System.Drawing.Point(359, 402)
        Me.Label6.Name = "Label6"
        Me.Label6.Size = New System.Drawing.Size(285, 31)
        Me.Label6.TabIndex = 11
        Me.Label6.Text = "Upload Proposal (.pdf)"
        '
        'Button1
        '
        Me.Button1.BackColor = System.Drawing.Color.Red
        Me.Button1.Font = New System.Drawing.Font("Microsoft Sans Serif", 10.125!, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, CType(0, Byte))
        Me.Button1.ForeColor = System.Drawing.SystemColors.ButtonHighlight
        Me.Button1.Location = New System.Drawing.Point(728, 543)
        Me.Button1.Name = "Button1"
        Me.Button1.Size = New System.Drawing.Size(187, 58)
        Me.Button1.TabIndex = 12
        Me.Button1.Text = "Batal"
        Me.Button1.UseVisualStyleBackColor = False
        '
        'FormAjukanEvent
        '
        Me.AutoScaleDimensions = New System.Drawing.SizeF(12.0!, 25.0!)
        Me.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font
        Me.BackColor = System.Drawing.SystemColors.ButtonHighlight
        Me.ClientSize = New System.Drawing.Size(1111, 645)
        Me.Controls.Add(Me.Button1)
        Me.Controls.Add(Me.Label6)
        Me.Controls.Add(Me.btnAjukanEvent)
        Me.Controls.Add(Me.btnBrowse)
        Me.Controls.Add(Me.txtDeskripsi)
        Me.Controls.Add(Me.txtPembiayaan)
        Me.Controls.Add(Me.txtJudulKegiatan)
        Me.Controls.Add(Me.txtJudulEvent)
        Me.Controls.Add(Me.lblFilePath)
        Me.Controls.Add(Me.Label4)
        Me.Controls.Add(Me.Label3)
        Me.Controls.Add(Me.Label2)
        Me.Controls.Add(Me.Label1)
        Me.Name = "FormAjukanEvent"
        Me.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen
        Me.Text = "FormAjukanEvent"
        Me.ResumeLayout(False)
        Me.PerformLayout()

    End Sub

    Friend WithEvents Label1 As Label
    Friend WithEvents Label2 As Label
    Friend WithEvents Label3 As Label
    Friend WithEvents Label4 As Label
    Friend WithEvents lblFilePath As Label
    Friend WithEvents txtJudulEvent As TextBox
    Friend WithEvents txtJudulKegiatan As TextBox
    Friend WithEvents txtPembiayaan As TextBox
    Friend WithEvents txtDeskripsi As TextBox
    Friend WithEvents btnBrowse As Button
    Friend WithEvents btnAjukanEvent As Button
    Friend WithEvents Label6 As Label
    Friend WithEvents Button1 As Button
End Class
