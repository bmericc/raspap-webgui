<?php

$dhcpd_leases_file = "/var/lib/dhcp/dhcpd.leases";
$password = "";
$mac_vendor = true;
$cache_vendor_results = true;

require_once("/var/www/html/raspap-webgui/includes/parser.class.php");
?>

<?php
//read leases file
if (isset($_POST['searchfilter'])) {
	$searchfilter = $_POST['searchfilter'];
} else {
	$searchfilter = "";
}
if (isset($_POST['sort_column'])) {
	$sort_column = $_POST['sort_column'];
} else {
	$sort_column = 0;
}
if (isset($_POST['onlyactiveleases'])) {
	$onlyactiveleases = true;
} else {
	$onlyactiveleases = false;
}

if (file_exists($dhcpd_leases_file) && is_readable($dhcpd_leases_file))
{
	$open_file = fopen($dhcpd_leases_file, "r") or die("Unable to open DHCP leases file.");
	if ($open_file)
	{
		if ($searchfilter != "") {
			$searchfiledmsg = $searchfilter;
		} else {
			$searchfiledmsg = "Type to search";
		}

		//Call the dhcplease file parser
		$parser = new ParseClass();
		$parser->parser($open_file);

		?>
		<table class="table table-responsive table-striped">
		<tr class="table_title">
		<td width="14%"><b>
			IP Address
		</b></td>

		<td width="14%"><b>
			Start Time
		</b></td>

		<td width="14%"><b>
			End Time
		</b></td>

		<td width="14%"><b>
			Lease Expires
		</b></td>

		<td width="14%"><b>
			MAC Address
		</b></td>
		<td width="14%"><b>
			Client Identifier
		</b></td>
		<td width="14%"><b>
			Hostname
		</b></td>

		</tr>
		<?php
		//Display the dhcp lease table using the filter and ordered
		$parser->print_table($searchfilter, $sort_column, $onlyactiveleases);
		fclose($open_file);
		?>
		</table>
		<?php
		echo "<p>Total number of entries in DHCP lease table: " . count($parser->dhcptable) . "</p>\n";
		echo "<p>Number of entries displayed on this page: " . $parser->filtered_number_display . "</p>\n";
	}
}
else
{
	echo "<p class='error'>The DHCP leases file does not exist or does not have sufficient read privileges.</p>";
}

//display message if the cache file isn't writeable
if ($cache_vendor_results && !is_writeable("./nmap-mac-prefixes_cache")) {
	echo "<p class='error'>The nmap-mac-prefixes_cache file doesn't have sufficient write privileges.</p>";
}
?>

<br>
