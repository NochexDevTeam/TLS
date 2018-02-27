<%@ Page Language="VB" AutoEventWireup="false" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head runat="server">
    <title></title>
</head>
<body>
    <form id="form1" runat="server">
    <div>
<% 

 '   Try
        ' Get all the POST details into a NameValueCollection
        Dim nvc As NameValueCollection = Request.Form
        Dim tlsString as string
		Net.ServicePointManager.SecurityProtocol = 3072
		
        ' Get all the POST details from the NameValueCollection and convert to String
        Dim postdetails As String = nvc.ToString
        
        ' Create a request to the Nochex server
        Dim webrequest As Net.HttpWebRequest = Net.WebRequest.Create("https://tlstest.nochex.com")
        webrequest.Method = "GET" ' Set as a POST request
        webrequest.ContentType = "application/x-www-form-urlencoded"
        Dim byteArray As Byte() = Encoding.UTF8.GetBytes(postdetails) ' Encode the POST details into bytes
        webrequest.ContentLength = byteArray.Length
        
        ' Get the response 
        Dim webresponse As Net.HttpWebResponse = webrequest.GetResponse
        Dim reader As New IO.StreamReader(webresponse.GetResponseStream) ' Create reader to get response
        Dim apcresponse As String = reader.ReadToEnd ' Return the APC response as a String
        reader.Close() ' Close the reader
		
		if apcresponse = "NOCHEX_Connection_OK" then
		 
		tlsString = "<h2>Congratulations your server already supports TLS 1.2. You will not be required to make any changes to your configuration. </h2>"
		
		else
		
		tlsString = apcresponse
		
		end if
		
		Response.Write(tlsString)
       
    
%>   
     
    </div>
    </form>
</body>
</html>
