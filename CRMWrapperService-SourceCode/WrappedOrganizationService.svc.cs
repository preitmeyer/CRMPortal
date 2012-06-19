using System;
using System.Collections.Generic;
using System.Linq;
using System.Runtime.Serialization;
using System.ServiceModel;
using System.ServiceModel.Web;
using System.Text;
using System.ServiceModel.Description;

using Microsoft.Xrm.Sdk;
using Microsoft.Xrm.Sdk.Client;
using Microsoft.Xrm.Sdk.Messages;

namespace CRMWrapperService
{
    public class WrappedOrganizationService : IWrappedOrganizationService
    {
        private OrganizationServiceProxy _serviceProxy;

        public WrappedOrganizationService()
        {
            IServiceManagement<IOrganizationService> orgServiceManagement =
                ServiceConfigurationFactory.CreateManagement<IOrganizationService>(
                new Uri(System.Configuration.ConfigurationManager.AppSettings["CRM.CRMURL"]));

            string userName = System.Configuration.ConfigurationManager.AppSettings["CRM.UserID"];
            string password = System.Configuration.ConfigurationManager.AppSettings["CRM.Password"];

            ClientCredentials credentials = new ClientCredentials();
            credentials.UserName.UserName = userName;
            credentials.UserName.Password = password;

            _serviceProxy = new OrganizationServiceProxy(orgServiceManagement, credentials);
        }

        #region Safety methods
        /// <summary>
        /// Runs the XRM call safely allowing error logging (eventually)
        /// </summary>
        /// <typeparam name="T"></typeparam>
        /// <param name="action"></param>
        /// <returns></returns>
        public T RunSafe<T>(Func<T> action)
        {
            try
            {
                return action.Invoke();
            }
            catch (Exception ex)
            {
                //TODO: Log

                throw ex;
            }
        }

        public void RunSafe(Action action)
        {
            try
            {
                action.Invoke();
            }
            catch (Exception ex)
            {
                //TODO: Log

                throw ex;
            }
        }
        #endregion


        public Guid Create(Entity entity)
        {
            return RunSafe(() => _serviceProxy.Create(entity));
        }

        public void Associate(string entityName, Guid entityId, Relationship relationship, EntityReferenceCollection relatedEntities)
        {
            RunSafe(() => _serviceProxy.Associate(entityName, entityId, relationship, relatedEntities));
        }

        public void Delete(string entityName, Guid id)
        {
            RunSafe(() => _serviceProxy.Delete(entityName, id));
        }

        public void Disassociate(string entityName, Guid entityId, Relationship relationship, EntityReferenceCollection relatedEntities)
        {
            RunSafe(() => _serviceProxy.Disassociate(entityName, entityId, relationship, relatedEntities));
        }

        public OrganizationResponse Execute(OrganizationRequest request)
        {
            return RunSafe(() => _serviceProxy.Execute(request));
        }

        public Entity Retrieve(string entityName, Guid id, Microsoft.Xrm.Sdk.Query.ColumnSet columnSet)
        {
            return RunSafe(() => _serviceProxy.Retrieve(entityName, id, columnSet));
        }

        public EntityCollection RetrieveMultiple(Microsoft.Xrm.Sdk.Query.QueryBase query)
        {
            return RunSafe(() => RetrieveMultiple(query));
        }

        public void Update(Entity entity)
        {
            RunSafe(() => _serviceProxy.Update(entity));
        }
    }
}
