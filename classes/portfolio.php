<?php
require_once('includes/db.php');

Class Portfolio {
  public function __construct() {
    global $ORACLE;
    $this->db = $ORACLE;
  }

  public function init() {
    $this->getStocks();
  }

  public function getByUser( $email ) {
    $stid = oci_parse($this->db, 'SELECT * FROM portfolio_portfolios WHERE owner=:email');
    oci_bind_by_name($stid, ':email', $email);
    $r = oci_execute($stid);
    $portfolios = array();
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
      $portfolios[$row['ID']] = $row;
    }
    oci_free_statement($stid);
    return $portfolios;
  }

  public function fromRow( $row ) {
    $this->id = $row['ID'];
    $this->name = $row['NAME'];
    $this->owner = $row['OWNER'];
    $this->cash = $row['CASH_BALANCE'];
    //print_r($this);
    $this->getStocks();
  }

  public function getStocks() {
    $stid = oci_parse($this->db, 'SELECT * FROM portfolio_stocks WHERE holder=:id');
    oci_bind_by_name($stid, ':id', $this->id);
    $r = oci_execute($stid);
    $stocks = array();
    while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
      $stock = new Stock;
      $stock->fromRow($row);
      $stocks[] = $stock;
    }
    oci_free_statement($stid);
    $this->stocks = $stocks;
    return $this->stocks;
  }

  public function delete() {
    $stid = oci_parse($this->db, 'DELETE FROM portfolio_portfolios WHERE id=:id');
    oci_bind_by_name($stid, ':id', $this->id);
    $r = oci_execute($stid);
    oci_free_statement($stid);
  }

  public function create($owner,$name,$description,$initial_deposit) {
    print "Owner is: $owner";
    $stid = oci_parse($this->db, 'INSERT INTO portfolio_portfolios (id, owner, name, description, cash_balance, creation_date) 
      VALUES(portfolio_ids.nextval, :owner, :name, :description, :deposit, :today)');
    oci_bind_by_name($stid, ':owner', $owner);
    oci_bind_by_name($stid, ':name', $name);
    oci_bind_by_name($stid, ':description', $description);
    oci_bind_by_name($stid, ':deposit', $initial_deposit);
    oci_bind_by_name($stid, ':today', time());
    $r = oci_execute($stid);
    //print $r;
    oci_free_statement($stid);
    return $r;
  }

  public function covCorMatrix($symbols, $matType, $opts) {

    $field1 = 'close';
    $field2 = 'close';

    if(isset($opts['field1']) {
        $field1 = mysql_real_escape_string($opts['field1']);
        }
    if(isset($opts['field2']) {
	$field2 = mysql_real_escape_string($opts['field2']);
	}
    if(isset($opts['to']) {
        $to = mysql_real_escape_string($opts['to']);
        }
    if(isset($opts['from'] {
        $from = mysql_real_escape_string($opts['from']);
        }

    foreach ($symbols as $outersym) {
	$sym1 = $outersym;
	foreach ($symbols as $innersym) {
		$sym2 = $innersym;
		#Grab all the mean/std of each stock pair in a join
		$query = "count(*), avg(l.'$field1'), std(l.'$field2'), avg(r.'$field2'), std(r.'$field2') from StocksDaily l join StocksDaily r on l.date=r.date where l.symbol='$sym1' and r.symbol='$sym2'";
		
		if(defined($to)) {
		$query .= " and date >= '$to'";
		}
		
		if(defined($from)) {
		$query .= " and date <= '$to'";
		}
 
		$result = mysql_query($query) or die (mysql_error());
		$row = mysql_fetch_array($result);

		$count = $row['count(*)'];
		$meanf1 = $row['avg(l.'$field1')'];
		$stdf1 = $row['std(l.'$field1')'];
		$meanf2 = $row['avg(r.'$field2')'];
		$stdf2 = $row['std(r.'$field2')'];

		if($count < 30) {
			$covar[$sy1][$sy2] = 'NODATA';
			$corrc[$sy1][$sy2] = 'NODATA';
		}
		else {
			$query = "avg((l.'$field1' - '$meanf1')*(r.'$field2' - '$meanf2')) from StocksDaily l join StocksDaily r on l.date=r.date where l.symbol='$sym1' and r.symbol='$sym2'";
			if(defined($to)) {
			$query .= " and date >= '$to'";
			}
			if(defined($from)) {
			$query .= " and date <= '$to'";
			}

			$result = mysql_query($query) or die (mysql_error());
			$row = mysql_fetch_array($result);

			$covar[$sym1][$sym2] = $row['avg((l.'$field1' - '$meanf1')*(r.'$field2' - '$meanf2')'];
			$corrc[$sym1][$sym2] = $covar[$sym1][$sym2] / ($stdf1 * $stdf2);
		}
	}
    }

}

?>
