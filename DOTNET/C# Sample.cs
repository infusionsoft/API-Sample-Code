//C# sample Created on 7/7/08 by: Justin Morris
//This C# sample will create a contact record in Infusionsoft 
//and based on check boxes add it to groups and/or campaigns.
//For this example you will need a windows forms project
//with 4 text boxes (fName, lName, eMail and Results - Results I have set as multiline.
//You will also need 4 check boxes: news1, news2, camp1 and camp2.
//And finally 1 button named addCon.

//start off by adding CookComputing.XmlRpcV2.dll as a project reference - you can download the file from
//www.xml-rpc.net.

//now lets import the cook computing xmlrpc library.
using CookComputing.XmlRpc;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace API_Sample
{
    public partial class Form1 : Form
    {
        //declare our API Key Variable
        string key = "13643d3c8910057ce07623ed01bb22b2";
        //Create an integer variable to hold our returned contact ID.
        int result=0;

        //Specify our xmlrpc server URL and build the interface.
        [XmlRpcUrl("https://mach2.infusionsoft.com:443/api/xmlrpc")]
        public interface iFace : IXmlRpcProxy
        {
            [XmlRpcMethod("ContactService.add")]
            int Add(string key, XmlRpcStruct map);

            [XmlRpcMethod("ContactService.addToGroup")]
            bool AddGrp(string key, int conID, int grpID);

            [XmlRpcMethod("ContactService.addToCampaign")]
            bool AddCamp(string key, int conID, int campID);
        } 

        public Form1()
        {
            InitializeComponent();
        }

        private void addCon_Click(object sender, EventArgs e)
        {
            //build the proxy for our xmlrpc interface
            iFace proxy = XmlRpcProxyGen.Create<iFace>();
            

            //Make sure our text boxes are not null.
            if (fName.Text != null && lName.Text != null && eMail.Text != null)
            {
                //Create a struct to hold the contact records data
                XmlRpcStruct conDat = new XmlRpcStruct();
                conDat.Add("FirstName", fName.Text);
                conDat.Add("LastName", lName.Text);
                conDat.Add("Email", eMail.Text);

                //make the call to add the contact.
                try
                {
                    result = proxy.Add(key, conDat);
                    Results.Text += "Contact added - ID: " + result + System.Environment.NewLine;
           
                }
                catch (Exception ex)
                {
                    MessageBox.Show(ex.Message);
                }


                //now we will add the contact to any groups or campaigns that were checked.
                //if we wanted to we could check if the calls return true/false to determine output.
                try
                {
                    if (news1.Checked)
                    {
                        //news1 is checked so add it to the correct group ID.
                        proxy.AddGrp(key, result, 91);
                        Results.Text += "Contact " + result + " added to group 91" + System.Environment.NewLine;
                    }
                    if (news2.Checked)
                    {
                        //news2 is checked so add it to the correct group ID.
                        proxy.AddGrp(key, result, 92);
                        Results.Text += "Contact " + result + " added to group 92" + System.Environment.NewLine;
                    }

                    if (camp1.Checked)
                    {
                        //camp1 is checked so add it to the correct campaign ID.
                        proxy.AddCamp(key, result, 21);
                        Results.Text += "Contact " + result + " added to campaign 21" + System.Environment.NewLine;
                    }
                    if (camp2.Checked)
                    {
                        //camp2 is checked so add it to the correct campaign ID.
                        proxy.AddCamp(key, result, 23);
                        Results.Text += "Contact " + result + " added to campaign 23" + System.Environment.NewLine;
                    }
                }
                catch (Exception ex)
                {
                    MessageBox.Show(ex.Message);
                }
                Results.Text += System.Environment.NewLine;
            }
            else { MessageBox.Show("Error: First Name, Last Name and Email are required!"); }

        }
    }
}
