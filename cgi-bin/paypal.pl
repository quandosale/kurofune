#!/usr/bin/perl

use strict;

# Modules
use CGI::Carp; 
use CGI qw(:standard escapeHTML);
use LWP::UserAgent;

# CGI.pm versioning issue
$CGI::USE_PARAM_SEMICOLONS = 0;

# Revision Notes
# 09/20/01 - initial release
# 10/24/01 - added error emails for flock and seek failures
# 01/09/02 - changed email support to sendmail
# 16/10/09 - Added handling for subscr_cancel IPN message so user will be deleted

# Version Number
# 1.2

# © 2002 PayPal, Inc. and others. All rights reserved.
# This code is subject to the Terms and Conditions in 
# the accompanying Beta Software License Agreement

# User Constants - customize these to YOUR installation (see manual)
# -------------------------------------------------------------------

# Set this to the path of your .htpassword file
my $PASSWORD_FILE = '/var/www/html/.htpassword';

# Set this to the path of your processed_txns file
my $TRANSACTION_FILE = '/var/www/html/processed_txns';

# You only need to change this if you are running with https
# see the manual for details
my $PAYPAL_URL = 'http://www.paypal.com/cgi-bin/webscr'; 

# If you have an initial trial period set it here. For example one 
# month would be '1 M'
my $PERIOD1 = '1 M'; 

# If you have a second trial period set it here. For example one 
# month would be '1 M'
my $PERIOD2 = '7 D'; 

# Set this to your recurring or normal period. For example one 
# month would be '1 M'
my $PERIOD3 = '1 M'; 

# Set this to the dollar amount for your initial trial period. For
# example a free trial would be '0.00'
my $AMOUNT1 = ''; 

# Set this to the dollar amount for your second trial period. For
# example a $1.00 trial would be '1.00'
my $AMOUNT2 = ''; 

# Set this to the dollar amount for your recurring or normal period. 
# For example $1.00 would be '1.00'
my $AMOUNT3 = ''; 

# Set this to the path of sendmail. On Linux and FreeBSD systems this 
# is typically '/usr/sbin/sendmail', on Solaris systems it is usually
# found at '/usr/lib/sendmail'
my $SENDMAIL_PATH = '';

# Set this to the email address you'd like to have error notification 
# messages sent to
my $ADMIN_EMAIL = 'peterna5@gmail.com';

# Set this to your primary PayPal email address
my @PAYMENT_EMAILS = ('info@kurofune.club');

# -------------------------------------------------------------------


main();

sub main {

	# acknowlege the ipn from PayPal
	if (ack_ipn()) {
		# decide what to do with msg received
		handle_ipn();
	}
	# IPN was successfully processed
	respond(1);
}

sub ack_ipn {
	# ack the ipn
	my $ua = new LWP::UserAgent;
	
	# build the request
	my $req = new HTTP::Request("POST", $PAYPAL_URL);
	$req->content_type("application/x-www-form-urlencoded");
	$req->content(query_string() . "&cmd=_notify-validate");
	# get the response
	my $resp = $ua->request($req);
	if (($resp->is_success) && ($resp->content eq "VERIFIED")) {
		return 1;
	} else {
		# attempt to identify error
		if (($resp->is_success) && ($resp->content eq "INVALID")) {
			error_notify("Notification received was NOT from PayPal - Message was ignored.", 
				"acknowledge IPN", 0, 0);	
		}
		else {
			error_notify("Notification could not be acknowledged due to a network or PayPal issue. "
						."PayPal will retry until it succeeds.", "acknowledge IPN", 1, 0);
		}	
		return undef;
	}
}

sub handle_ipn {

	# handle the msg received
	if ((param("txn_type") eq "subscr_signup") && (validate_signup())) {
		# make sure a username was sent
		if (!param("username")) {
			error_notify("No username found. Check your subscription button or link.", "add user", 0, 1);	
			return;
		}
		# add subscriber to password file
		add_user(param("username"), param("password"), param("subscr_id"));
	} elsif ((param("txn_type") eq "subscr_eot") || (param("txn_type") eq "subscr_cancel")){
		# make sure a username was sent
		if (!param("username")) {
			error_notify("No username found. Check your subscription button or link.", "remove user", 0, 1);	
			return;
		}
		# remove subscriber from password file
		remove_user(param("username"));
	} else {
		# ignore message 
	}
}

sub validate_signup {
	# validate the terms and amounts
	if ((param("period1") ne $PERIOD1) 
		|| (param("period2") ne $PERIOD2) 
		|| (param("period3") ne $PERIOD3) 
		|| (param("amount1") ne $AMOUNT1) 
		|| (param("amount2") ne $AMOUNT2) 
		|| (param("amount3") ne $AMOUNT3)) {
			error_notify("This customer did not sign-up according to your payment terms. " .
	    		"Although payment was accepted the account was not activated.",
				"validate subscription terms", 0, 1);
			return undef;
	} 

	# validate the receiver email
	my $valid = undef;
	foreach (@PAYMENT_EMAILS) {
		if (param("receiver_email") eq $_) {
			$valid = 1;
		}
	}
	if (!$valid)
	{
		error_notify("An IPN was received that did not match your primary email " .
	   		"address - Message was ignored.",
			"validate receiver email ", 0, 0);
		return undef;
	}
	

	file_open($TRANSACTION_FILE, "+<");
	# validate transaction id
	if (find_txn(param("subscr_id"))) {
		# transaction was previously processed 
		file_close($TRANSACTION_FILE);
		error_notify("An IPN was received that was already processed " .
	   		"- Message was ignored.", "validate subscription id", 0, 0);
		return undef;
	} else {
		file_close($TRANSACTION_FILE);
	}

	return 1;
}

sub error_notify {
	# sends notification that an error has occured
	my $err_str = shift;
	my $action = shift;	
	my $kill = shift;	
	my $req_action = shift;	

	my $message = "The following error message was generated while trying to $action: \n\t$err_str\n\n\n";
	$message .= "User Information\n";
	$message .= "\tSubscriber's Username: " . param("username") . "\n";
	$message .= "\tSubscriber's Email: " . param("payer_email") . "\n";
	$message .= "\tSubscription Number: " . param("subscr_id") . "\n";
	$message .= "\tTransaction Type: " . param("txn_type") . "\n";

	my $subject = "Subscription Error";
	if ($req_action) {
		$subject .= " - Requires Action";
	} else {
		$subject .= " - No Action Required";
	}
	
	# if an email is not specified write to error_log only
	if (($ADMIN_EMAIL) && ($SENDMAIL_PATH)) {
		my %mail = ( To       => $ADMIN_EMAIL,
					 From     => $ADMIN_EMAIL,
				     Subject  => $subject, 
				     Message  => $message,
				   );

		sendmail(%mail); 
	}

	# put it into the error log
	if ($kill) {
		# IPN will retry
		respond(0);
		croak $message;
	} else {
		carp $message;
	}
}

sub sendmail {
	# send email using sendmail 
	my %mail;
	my $key;

	while (@_) {
		$key = shift @_;
		$mail{$key} = shift @_;
	}

	if (!open(SENDMAIL, "|$SENDMAIL_PATH -t")) {
		carp "Unable to open sendmail pipe.";
	}

	print SENDMAIL "To: $mail{'To'}\n";
	print SENDMAIL "From: $mail{'From'}\n";
	print SENDMAIL "Subject: $mail{'Subject'}\n";
	print SENDMAIL "Content-type: text/plain\n\n";
	print SENDMAIL "$mail{'Message'}";

	if (!close(SENDMAIL)) {
		carp "Unable to close sendmail pipe.";
	}
}

sub file_open {
	my $open_file = shift;
	my $open_str = shift;

	# open the file
	if (!open(FILE, "$open_str$open_file")) {
		error_notify("Unable to access: $open_file - $!\n", "open file", 1, 1);
	}

	# lock access to this file
	if (!flock(FILE, 2)) {
		error_notify("Unable to get lock on file: $open_file\n", "open file", 1, 1);
	}
	if (!seek(FILE, 0, 0)) {
		error_notify("Unable to seek to the start of the file: $open_file\n", "open file", 1, 1);
	}
}

sub file_close {
	my $close_file = shift;

	# unlock the file
	if (!flock(FILE, 8)) { 
		error_notify("Unable to unlock file: $close_file\n", "close file", 1, 1);
	}

	if (!close(FILE)) {
		error_notify("Unable to close: $close_file - $!\n", "close file", 0, 0);
	}
}

sub find_login {
	my $new = shift;
	my $login;
	my $password;
	my $remainder;

	# for each line, break into parts
	while(<FILE>) {
		chop;
		($login, $password, $remainder) = split(/:/, $_, 3);
		if ($login eq $new) {
			return 1;
		}
	}

	return undef;
}

sub find_txn {
	my $new_txn = shift;

	# look for this txn id
	while(<FILE>) {
		if (/^$new_txn/) {
			return 1;
		}
	}

	return undef;
}

sub add_user {
	my $login = shift;
	my $password = shift;
	my $txn = shift;


	file_open($PASSWORD_FILE, "+<");
	# check to see if this user already exists
	if (find_login($login)) {
		error_notify("Username: $login already exists", "add user", 0, 1);	
	} else {
		# seek to the end of the file
		if (!seek(FILE, 0, 2)) {
			error_notify("Unable to seek to the end of the file: $PASSWORD_FILE\n", "add user", 1, 1);
		}
		# add the necessary line
		print FILE "$login\:$password\n";
	}
	file_close($PASSWORD_FILE);

	file_open($TRANSACTION_FILE, "+<");
	# seek to the end of the file
	if (!seek(FILE, 0, 2)) {
		error_notify("Unable to seek to the end of the file: $TRANSACTION_FILE\n", "add user", 1, 1);
	}
	# add the necessary line
	print FILE "$txn\n";
	file_close($TRANSACTION_FILE);

}

sub remove_user {
	my $login = shift;	
	my @others;

	file_open($PASSWORD_FILE, "+<");

	while(<FILE>) {
		if (!/^$login\:/) {
			# stuff lines into array
			push(@others, $_);
		} 
	}
	
	if (@others) {
		# seek to start of file
		if (!seek(FILE, 0, 0)) {
			error_notify("Unable to seek to the start of the file: $PASSWORD_FILE\n", "remove user", 1, 1);
		}
		
		# write out all the users
		foreach (@others) {
			print FILE $_;
		}
		
		# truncate the file to the current position
		truncate(FILE, tell(FILE));
	}

	file_close($PASSWORD_FILE);
}

sub respond {
	# handle the http reponse
	my $is_success = shift;

	if ($is_success) {
		print header(-status=>('204 No Content'));
	}
	else {
		print header(-status=>('500 Internal Server Error'));
	}
}
