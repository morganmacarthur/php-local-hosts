<?php

// hosts_local.txt updater
// hosts_input.txt is the list of the domain names and where you can define ip addresses without looking them up
// the files ought to be writable by php

$hosts_input = 'hosts_input.txt';
$hosts_local = 'hosts_local.txt';

$hosts_input = file_get_contents($hosts_input);

// clean white space at the end and normalize to spaces
$hosts_input = trim(str_replace("\t",' ',$hosts_input));
$hosts_lines = explode("\n", $hosts_input);
$hosts_output = '';
foreach ($hosts_lines as $hosts_line)
{
  // fields should be separated by spaces at this point
  $hosts_fields = explode(' ', $hosts_line);
  // trim any leftover whitespace and grab domain name
  $hosts_name = trim($hosts_field[1]);
  // trim any leftover whitespace and grab ip address
  $hosts_ip = trim($hosts_field[0]);
  if ((filter_var('mail@' . $hosts_name, FILTER_VALIDATE_EMAIL))&&(str_replace('.','',$hosts_ip) > 0)&&(filter_var($hosts_ip, FILTER_VALIDATE_IP)))
  {
    // when there is a domain and a valid ip address, write output unchanged
    $hosts_output .= "{$hosts_ip} {$hosts_name}\n";
  }
  elseif (filter_var('mail@' . $hosts_name, FILTER_VALIDATE_EMAIL))
  {
    // in this case there is no predefined ip override, so look it up
    $hosts_ip = gethostbyname($hosts_name . '.');
    if ((str_replace('.','',$hosts_ip) > 0)&&(filter_var($hosts_ip, FILTER_VALIDATE_IP)))
    {
      // gethostbyname returned a valid ip, write out the line
      $hosts_output .= "{$hosts_ip} {$hosts_name}\n";
    }
    else
    {
      // try it a second time
      $hosts_ip = gethostbyname($hosts_name . '.');
      if ((str_replace('.','',$hosts_ip) > 0)&&(filter_var($hosts_ip, FILTER_VALIDATE_IP)))
      {
        // gethostbyname returned a valid ip, write out the line
        $hosts_output .= "{$hosts_ip} {$hosts_name}\n";
      }
    }
  }
  else
  {
    // export any misunderstood line as a comment
    $hosts_line = trim(trim($hosts_line, '#'));
    $hosts_output .= "# {$hosts_line}\n";
  }
}

$hosts_output = trim($hosts_output);
// 11 is just a number beneath which there is no possible valid lines to write 1.1.1.1 y.z
if ($strlen($hosts_ouput) > 10)
{
  file_put_contents($hosts_local, $hosts_output, LOCK_EX);
}

?>