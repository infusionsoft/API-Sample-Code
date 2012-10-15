<%@ Page Language="VB" AutoEventWireup="false" CodeFile="Default.aspx.vb" Inherits="_Default" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title>Untitled Page</title>
</head>
<body>
    <form id="form1" runat="server">
    <div>
    <table>
    <tr><td>First name</td><td><asp:TextBox ID="fName" runat="server"></asp:TextBox></td></tr>
    <tr><td>Last name</td><td><asp:TextBox ID="lName" runat="server"></asp:TextBox></td></tr>
    <tr><td>Email</td><td><asp:TextBox ID="eMail" runat="server"></asp:TextBox></td></tr>
    <tr><td><asp:CheckBox ID="news1" runat="server" Text="News Letter 1" /> </td><td><asp:CheckBox ID="news2" runat="server" Text="News Letter 2" /> </td></tr>
    <tr><td><asp:CheckBox ID="camp1" runat="server" Text="Campaign 1" /></td><td><asp:CheckBox ID="camp2" runat="server" Text="Campaign 2" /></td></tr>
    <tr><td><asp:Button ID="addCon" runat="server" Text="Submit" /></td></tr>
    </table>
        <asp:TextBox ID="Results" runat="server" Height="150px" TextMode="MultiLine" 
                Width="245px"></asp:TextBox>
    </div>
        
    
    </form>
</body>
</html>
