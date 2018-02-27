using System;
using System.IO;
using System.Net;
using System.Net.Security;
using System.Text;
using System.Net.Mail;
using System.Collections.Specialized;

public partial class tlstest_csharp : System.Web.UI.Page
{
    protected void Page_Load(object sender, EventArgs e)
    {
			
			/*
			SecurityProtocolType.Ssl3 = 48,
		    SecurityProtocolType.Tls = 192,
		    SecurityProtocolType.Tls11 = 768,
		    SecurityProtocolType.Tls12 = 3072,			
			*/
			
			ServicePointManager.SecurityProtocol = (SecurityProtocolType)3072;
			//(SecurityProtocolType)3072;
			
			NameValueCollection nvc = Request.Form;
			string postdetails = nvc.ToString();
			
		 // Create a request using a URL that can receive a post. 
            WebRequest request = WebRequest.Create("https://tlstest.nochex.com");
            // Set the Method property of the request to POST.
            request.Method = "GET";			       
			request.ContentType = "application/x-www-form-urlencoded";
            // Create POST data and convert it to a byte array.
           
		   
            byte[] byteArray = Encoding.UTF8.GetBytes(postdetails);
            // Set the ContentType property of the WebRequest.
            request.ContentType = "application/x-www-form-urlencoded";
            // Set the ContentLength property of the WebRequest.     
			request.ContentLength = byteArray.Length;
			
			using (HttpWebResponse response = (HttpWebResponse)request.GetResponse())
			using (Stream stream = response.GetResponseStream())
			using (StreamReader reader = new StreamReader(stream))
			{
				string responseFromServer = reader.ReadToEnd();
				
				if(responseFromServer == "NOCHEX_Connection_OK"){
				
				Response.Write("<h2>Congratulations your server already supports TLS 1.2. You will not be required to make any changes to your configuration.</h2>");
				
				}else{
				
				Response.Write("<h2>" + responseFromServer + "</h2>" + GetCredential("https://tlstest.nochex.com"));
				
				}
				
			}
    }
}
