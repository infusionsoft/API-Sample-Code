'VB.NET sample Created on 7/7/08 by: Justin Morris
'This VB.NET sample will create a contact record in Infusionsoft 
'and based on check boxes add it to groups and/or campaigns.
'For this example you will need a windows forms project
'with 4 text boxes (fName, lName, eMail and Results - Results I have set as multiline.
'You will also need 4 check boxes: news1, news2, camp1 and camp2.
'And finally 1 button named addCon.

'start off by adding CookComputing.XmlRpcV2.dll as a project reference - you can download the file from
'www.xml-rpc.net.

'now lets import the cook computing xmlrpc library.
Imports CookComputing.XmlRpc

Public Class Form1
    'lets define the API key for our application.
    Dim key As String = "13643d3c8910057ce07623ed01bb22b2"

    'define the URL of our applications xmlrpc server and build an interface for it.
    <XmlRpcUrl("https://mach2.infusionsoft.com:443/api/xmlrpc")> _
    Public Interface iFace
        Inherits IXmlRpcProxy
        'Build the method to add a contact record
        <XmlRpcMethod("ContactService.add")> _
        Function add(ByVal key As String, ByVal contact As XmlRpcStruct)
        'Build the method to add a contact to a group
        <XmlRpcMethod("ContactService.addToGroup")> _
        Function addGrp(ByVal key As String, ByVal conID As Integer, ByVal grpID As Integer)
        'Build the method to add a contact to a campaign
        <XmlRpcMethod("ContactService.addToCampaign")> _
        Function addCamp(ByVal key As String, ByVal conID As Integer, ByVal campID As Integer)
    End Interface

    Private Sub addCon_Click(ByVal sender As System.Object, ByVal e As System.EventArgs) Handles addCon.Click
        'Now we need to build our xmlrpc proxy.
        Dim proxy As iFace = CType(XmlRpcProxyGen.Create(GetType(iFace)), iFace)
        'now that we have our proxy built we can use our methods in the interface to send calls to the API.
        'Lets start by verifying that the text fields are not null.

        If fName.Text <> Nothing And lName.Text <> Nothing And eMail.Text <> Nothing Then
            'they arent null so we can now build the xmlrpc struct for the contact.
            Dim contact As New XmlRpcStruct
            contact.Add("FirstName", fName.Text)
            contact.Add("LastName", lName.Text)
            contact.Add("Email", eMail.Text)

            'now that we build the contact lets create an integer variable to store the returned contact ID in.
            Dim retID As Integer = Nothing

            'now we will try to add the contact to Infusionsoft.
            Try
                retID = proxy.add(key, contact)
                Results.Text &= "Added new contact - ID: " & retID & vbNewLine
            Catch ex As Exception
                MsgBox("Error adding contact." & vbNewLine & ex.Message, MsgBoxStyle.Critical, "Error")
            End Try

            'now assuming our contact was successfully added retID should be the contacts ID.
            'we will make sure it is not null then assign the contact to the groups/campaigns.
            If retID <> Nothing Then
                Try
                    If news1.Checked = True Then
                        'news1 is checked so add it to the correct group ID.
                        proxy.addGrp(key, retID, 91)
                        Results.Text &= "Added contact " & retID & " to group 91" & vbNewLine
                    End If

                    If news2.Checked = True Then
                        'news2 is checked so add it to the correct group ID.
                        proxy.addGrp(key, retID, 92)
                        Results.Text &= "Added contact " & retID & " to group 92" & vbNewLine
                    End If

                    If camp1.Checked = True Then
                        'camp1 is checked so add it to the correct campaign ID.
                        proxy.addCamp(key, retID, 21)
                        Results.Text &= "Added contact " & retID & " to campaign 21" & vbNewLine
                    End If

                    If camp2.Checked = True Then
                        'camp2 is checked so add it to the correct campaign ID.
                        proxy.addCamp(key, retID, 23)
                        Results.Text &= "Added contact " & retID & " to campaign 23" & vbNewLine
                    End If
                Catch ex As Exception
                    MsgBox(ex.Message, MsgBoxStyle.Critical, "ERROR")
                End Try
                Results.Text &= vbNewLine
            End If
        Else
            MsgBox("Error: First Name, Last Name and Email are required", MsgBoxStyle.Critical, "Error")
        End If
    End Sub
End Class
