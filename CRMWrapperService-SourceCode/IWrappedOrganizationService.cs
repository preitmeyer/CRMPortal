using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime.Serialization;
using System.ServiceModel;
using System.ServiceModel.Web;
using System.Text;

using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Messages;

namespace CRMWrapperService
{
    [ServiceContract(Namespace = "http://schemas.microsoft.com/xrm/2011/Contracts/Services")]
    [ServiceKnownType(typeof(RetrieveMultipleRequest))]
    [ServiceKnownType(typeof(RetrieveMultipleResponse))]
    [ServiceKnownType(typeof(EntityCollection))]
    public interface IWrappedOrganizationService : IOrganizationService
    {
        //nothing specific here, just wrapping IOrganizationService
    }

}
