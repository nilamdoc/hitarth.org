<?php


/* Remote File Name and Path */
$remote_file = 'files.zip';
 
/* FTP Account (Remote Server) */
$ftp_host = 'cdn.hitarth.org'; /* host */
$ftp_user_name = 'hitarth@cdn.hitarth.org'; /* username */
$ftp_user_pass = 'GreenCoinX'; /* password */
 
 
/* File and path to send to remote FTP server */
$local_file = 'files.zip';
 
/* Connect using basic FTP */
$connect_it = ftp_connect( $ftp_host );
 
/* Login to FTP */
$login_result = ftp_login( $connect_it, $ftp_user_name, $ftp_user_pass );
 
/* Send $local_file to FTP */
if ( ftp_put( $connect_it, $remote_file, $local_file, FTP_BINARY ) ) {
    echo "WOOT! Successfully transfer $local_file\n";
}
else {
    echo "Doh! There was a problem\n";
}
 
/* Close the connection */
ftp_close( $connect_it );