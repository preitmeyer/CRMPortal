using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace CRMWrapperService
{
    public class AuthModule : IHttpModule
    {

        public void Dispose()
        {
            
        }

        public void Init(HttpApplication context)
        {
            context.BeginRequest +=new EventHandler(OnBeginRequest);
            context.EndRequest += new EventHandler(OnEndRequest);
        }

        

        private void OnBeginRequest(object sender, EventArgs e)
        {
            //TODO: implement auth
        }

        private void OnEndRequest(object sender, EventArgs e)
        {

        }
    }
}