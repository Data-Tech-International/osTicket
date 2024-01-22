![Microsoft Teams](https://developer.microsoft.com/en-us/graph/blogs/wp-content/uploads/2018/11/Teams-Dev-Logo-900x360.png)

osTicket-microsoft-teams
==============
An plugin for [osTicket](https://osticket.com) which posts notifications to a [Microsoft Teams](https://products.office.com/en-us/microsoft-teams/group-chat-software) channel.

Originally forked from: [https://github.com/clonemeagain/osticket-slack](https://github.com/clonemeagain/osticket-slack).

Notifications are posted for the following ticket actions:
- Ticket created/opened
- Ticket updated/edited
- Ticket transfered
- Ticket task created/opened
- Ticket task closed
- Ticket closed

Info
------
This plugin uses CURL and was designed/tested with osTicket versions:
- 1.17.0
- 1.17.1
- 1.17.2

## Requirements
- php_curl
- A Office 365 account

## Install
--------
1. The plugin needs to be enabled & configured, so login to osTicket, select "Admin Panel" then "Manage -> Plugins" you should be seeing the list of currently installed plugins.
2. Click on `Teams Notifier` and paste your Teams Endpoint URL into the box (MS Teams setup instructions below).
3. Click `Save Changes`! (If you get an error about curl, you will need to install the Curl module for PHP).
4. After that, go back to the list of plugins and tick the checkbox next to "MS Teams Notifier" and select the "Enable" button.

-------------------------------------------
## Supporting Code Addition:
Please replace the two files class.ticket.php and class.task.php under include folder of osticket.

Add New signals , Line numbers As per Osticket 1.17.2

1. For Ticket Edit Notification
   File Location -  /include/class.ticket.php ,
   Line no 3800 to 3803
   Inside function update($vars, &$errors) {

    Add the below (+) code after following code
    if ($changes) {
          $this->logEvent('edited', $changes);
        }

      // MSTeams custom changes to add edit signal starts
        $info = array('changes'=>$changes);
        Signal::send('ticket.edit', $this, $info);
      // MSTeams custom changes to add edit signal ends

2. For Ticket Close Notification
   File Location -  /include/class.ticket.php
   Line No - 1537 to 1540
   Inside  function setStatus($status..
   under switch ($status->getState()) {
            case 'closed':

    Add the below (+) code before break statement of case 'closed':


    // MSTeams Custom changes to add "ticket close" signal starts
     $info = array('status'=>$status->getId());
     Signal::send('ticket.close', $this, $info);
    // MSTeams Custom changes to add "ticket close" signal ends

  break;

3. For Ticket Transfer Notification
   File location - /include/class.ticket.php
   Line 2723 to 2726
   In function transfer(TransferForm $form, &$errors, $alert=true) {

  	// MSTeams custom changes for ticket Dept transfer starts
  	$signal_info = array('dept' => $dept);
  	Signal::send('ticket.transfer', $this, $signal_info );
  	// MSTeams custom changes for ticket Dept transfer ends

  	Add the above (+) code Before the below lines
    //Send out alerts if enabled AND requested
        if (!$alert || !$cfg->alertONTransfer() || !$dept->getNumMembersForAlerts())
            return true; //no alerts!!

4. For Close Task Notification
   File location - /include/class.task.php,
   Line No-  623 to 626
   In function setStatus($status, $comments='', &$errors=array()) {
   Under Switch - case 'closed':

   Add the below (+) code after If Closing braces -  if ($t->ticket) { ..... }

 	// MSTeams Custom change for task close starts
 	$info = array('title'=>$vars['title']);
 	Signal::send('task.close', $this, $info);
 	// MSTeams Custom change for task close ends

    };
    break;

----------------------------------------------

## MS Teams Setup:
- Open MS Teams, navigate to channel and open Connectors from elipsoids (...) menu
- Select Incoming Webhook and configure
- Choose webhook name and optionally change associated image
- Click Create
- Scroll down and copy the Webhook URL entirely, paste this into the `osTicket -> Admin -> Plugins -> Teams Notifier` config admin screen.

The channel you select will receive an event notice, like:
```
Ivan Pavlovic has set up a connection to Incoming Webhook so group members will be notified for this configuration with name osTicket
```

Notes, Replies from Agents and System messages shouldn't appear!