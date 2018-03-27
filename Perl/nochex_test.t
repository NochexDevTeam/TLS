#===============================================================================
#
#         FILE: nochex_test.t
#
#  DESCRIPTION: Test of TLS v1.2 in our client
#
#       AUTHOR: Pete Houston (), ph1@openstrike.co.uk
# ORGANIZATION: Openstrike
#      VERSION: 1.0
#      CREATED: 27/11/17 11:46:47
#     REVISION: ---
#===============================================================================

use strict;
use warnings;

use LWP::UserAgent;
use Test::More tests => 2;

my $ua = LWP::UserAgent->new;
$ua->ssl_opts ({ SSL_version => 'TLSv1_2' });
my $res = $ua->get ('https://tlstest.nochex.com');

ok ($res->is_success, 'Request returned');
like ($res->content, qr/NOCHEX_Connection_OK/, 'Content is correct');
