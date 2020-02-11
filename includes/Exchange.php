<?
class Exchange {
  protected $fiatList = [];

  /**
   * Checks if the Exchange has the provided fiat as available
   * @param  String  $symbol Fiat symbol
   * @return boolean       Returns true is available
   */
  public function hasFiat($symbol) {
    return in_array($symbol, $this->getFiatList());
  }

  /**
   * Gets the full list of available Fiat currencies for this exchange
   * @return array Array of symbols
   */
  public function getFiatList() {
    return $this->fiatList;
  }

  /**
   * CURL request
   * @param  string $url
   * @param  string $action
   * @param  object $data
   * @return string         Request Output
   */
  protected function request($url, $action='GET', $data=null) {
    $ch			=			curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    switch($action){
      case "POST":
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          break;
      case "GET":
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
          break;
      case "PUT":
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
          break;
      case "DELETE":
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
          break;
      default:
          break;
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //This is set to 0 for development mode. Set 1 when production (self-signed certificate error)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_CAINFO, openssl_get_cert_locations()['default_cert_file']);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $output = curl_exec($ch);

    curl_close($ch);
    return $output;
  }
}
?>
