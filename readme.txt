CRMPortal is a Wordpress Plugin for Microsoft Dynamics CRM 2011.
If you would like to integrate your current Wordpress Website with a hosted or on-premise (or online) copy of Microsoft Dynamics CRM 2011, then you're in the right place. 

\\\\\\Current Features\\\\\\

Wordpress Plugin to perform the following tasks:
	- Create Leads as a website visitor (contact us page)
	- Create Cases as a logged-in user (form)
	- Review Cases as a logged-in user (grid)
	- Create notes as a logged-in user for a specific case (form)
	- Review notes as a logged-in user for a specific case (grid)

Connection directly to CRM Online (Windows Live ID hosted)
Connection directly to CRM Hosted IFD (internet facing deployment) via externally hosted webservice wrapper

\\\\\\Future Features\\\\\\

Ability to switch between Online and IFD Hosted CRM deployments as setting page option

Ability to connect to CRM 2011 Online Office 365 variant (OSDP / ADFS)

Configuration settings for auto-responders and messages as setting page option

Add ability to configure solution to add cases per contact vs. cases per account

Generate easier installation instructions


\\\\\\Known Issues\\\\\\

Configuration is all done via PHP code modification per deployment, many settings are hard-coded and will need to be updated. 

The initial set-up for a user is still done via manual processing and association of their wordpress user account and their CRM Account GUID


\\\\\\Known Bugs\\\\\\

You must enable a global variable in your functions.php for wordpress in order to use the note creation code. A general understanding of PHP and wordpress is required to get this deployment functioning for your organization.

