<?php
/*
*---------------------------------------------------------
*
*	CartET - Open Source Shopping Cart Software
*	http://www.cartet.org
*
*---------------------------------------------------------
*/

defined('_VALID_OS') or die('Прямой доступ  не допускается.');

  class sales_report {
    var $mode, $globalStartDate, $startDate, $endDate, $info, $previous, $next, $startDates, $endDates, $size;

    function sales_report($mode, $startDate = "", $endDate = "", $filter = "") {
      $this->mode = $mode;
      $this->previous = "";
      $this->next = "";
      $this->filter = "";
      $this->info = array(array());
      $first_query = os_db_query("select UNIX_TIMESTAMP(min(date_purchased)) as first FROM " . TABLE_ORDERS);
      $first = os_db_fetch_array($first_query);
      $this->globalStartDate = mktime(0, 0, 0, date("m", $first['first']), date("d", $first['first']), date("Y", $first['first']));

      $tmp_query = os_db_query("SELECT * FROM " . TABLE_ORDERS_STATUS . " where language_id = " . (int)$_SESSION['languages_id']);
      $i = 0;
      while ($status = os_db_fetch_array($tmp_query)) {
        $tmp[$i]['index'] = $status['orders_status_id'];
        $tmp[$i]['value'] = $status['orders_status_name'];
        $i++;
      }
      $this->status_available = $tmp;
      $this->status_available_size = $i;

      if ($endDate == "" or $startDate == "") {
        $dateGiven = false;
        $startDate = 0;
        $this->endDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
      } else {
        $dateGiven = true;
        if ($endDate > mktime(0, 0, 0, date("m"), date("d"), date("Y"))) {
          $this->endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
        } else {
          $this->endDate = mktime(0, 0, 0, date("m", $endDate), date("d", $endDate) + 1, date("Y", $endDate));
        }
      }
      switch ($this->mode) {
        case '1':
          if ($dateGiven) {
            $this->startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate), date("Y", $startDate));
            $this->endDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) + 1, date("Y", $startDate));
            $this->size = 24;
          } else {
            $this->startDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $this->endDate = mktime(date("G") + 1, 0, 0, date("m"), date("d"), date("Y"));
            $this->size = date("G") + 1;
            if ($this->startDate < $this->globalStartDate) {
              $this->startDate = $this->globalStartDate;
            }
          }
          for ($i = 0; $i < $this->size; $i++) {
            $this->startDates[$i] = mktime($i, 0, 0, date("m", $this->startDate), date("d", $this->startDate), date("Y", $this->startDate));
            $this->endDates[$i] = mktime($i + 1, 0, 0, date("m", $this->startDate), date("d", $this->startDate), date("Y", $this->startDate));
          }
          break;
        case '2':
          if ($dateGiven) {
            $this->startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate), date("Y", $startDate));
            $this->size = ($this->endDate - $this->startDate) / (60 * 60 * 24);
          } else {
            $this->startDate = mktime(0, 0, 0, date("m"), date("d") - date("w"), date("Y"));
            $this->endDate = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
            $this->size = date("w") + 1;
            if ($this->startDate < $this->globalStartDate) {
              $this->startDate = $this->globalStartDate;
            }
          }
          for ($i = 0; $i < $this->size; $i++) {
            $this->startDates[$i] = mktime(0, 0, 0, date("m", $this->startDate), date("d", $this->startDate) + $i, date("Y", $this->startDate));
            $this->endDates[$i] = mktime(0, 0, 0, date("m", $this->startDate), date("d", $this->startDate) + ($i + 1), date("Y", $this->startDate));
          }
          break;
        case '3':
          if ($dateGiven) {
            $this->startDate = mktime(0, 0, 0, date("m", $startDate), date("d", $startDate) - date("w", $startDate), date("Y", $startDate));

          } else {
            $firstDayOfMonth = mktime(0, 0, 0, date("m"), 1, date("Y"));
            $this->startDate = mktime(0, 0, 0, date("m"), 1 - date("w", $firstDayOfMonth), date("Y"));
          }
          if ($this->startDate < $this->globalStartDate) {
            $this->startDate = $this->globalStartDate;
          }
          $this->size = ceil((($this->endDate - $this->startDate + 1) / (60 * 60 * 24)) / 7);
          for ($i = 0; $i < $this->size; $i++) {
            $this->startDates[$i] = mktime(0, 0, 0, date("m", $this->startDate), date("d", $this->startDate) +  $i * 7, date("Y", $this->startDate));
            $this->endDates[$i] = mktime(0, 0, 0, date("m", $this->startDate), date("d", $this->startDate) + ($i + 1) * 7, date("Y", $this->startDate));
          }
          break;
        case '4':
          if ($dateGiven) {
            $this->startDate = mktime(0, 0, 0, date("m", $startDate), 1, date("Y", $startDate));
          } else {
            $this->startDate = mktime(0, 0, 0, 1, 1, date("Y"));
          }
          if ($this->startDate < $this->globalStartDate) {
            $this->startDate = mktime(0, 0, 0, date("m", $this->globalStartDate), 1, date("Y", $this->globalStartDate));
          }
          $this->size = (date("Y", $this->endDate) - date("Y", $this->startDate)) * 12 + (date("m", $this->endDate) - date("m", $this->startDate)) + 1;
          $tmpMonth = date("m", $this->startDate);
          $tmpYear = date("Y", $this->startDate);
          for ($i = 0; $i < $this->size; $i++) {
            $this->startDates[$i] = mktime(0, 0, 0, $tmpMonth + $i, 1, $tmpYear);
            $this->endDates[$i] = mktime(0, 0, 0, $tmpMonth + $i + 1, 1, $tmpYear);
          }
          break;
        case '5':
          if ($dateGiven) {
            $this->startDate = mktime(0, 0, 0, 1, 1, date("Y", $startDate));
            $this->endDate = mktime(0, 0, 0, 1, 1, date("Y", $endDate) + 1);
          } else {
            $this->startDate = mktime(0, 0, 0, 1, 1, date("Y") - 5 + 1);
            $this->endDate = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
          }
          if ($this->startDate < $this->globalStartDate) {
            $this->startDate = $this->globalStartDate;
          }
          $this->size = date("Y", $this->endDate) - date("Y", $this->startDate) + 1;
          $tmpYear = date("Y", $this->startDate);
          for ($i = 0; $i < $this->size; $i++) {
            $this->startDates[$i] = mktime(0, 0, 0, 1, 1, $tmpYear + $i);
            $this->endDates[$i] = mktime(0, 0, 0, 1, 1, $tmpYear + $i + 1);
          }
          break;
      }

      if (($this->mode < 3) or ($this->mode == 4)) {
        $tmpDiff = $this->endDate - $this->startDate;
        if ($this->size == 0) {
          $tmpUnit = 0;
        } else {
          $tmpUnit = $tmpDiff / $this->size;
        }

        switch($this->mode) {
          case '1':
            $tmp1 =  24 * 60 * 60;
            break;
          case '2':
            $tmp1 = 7 * 24 * 60 * 60;
            break;
          case '3':
            $tmp1 = 30 * 24 * 60 * 60;
            break;
          case '4':
            $tmp1 = 365 * 24 * 60 * 60;
            break;
        }
        $tmp = ceil($tmpDiff / $tmp1);
        if ($tmp > 1) {
          $tmpShift = ($tmp * $tmpDiff) + $tmpUnit;
        } else {
          $tmpShift = $tmp1 + $tmpUnit;
        }

        $tmpStart = $this->startDate - $tmpShift + $tmpUnit;
        $tmpEnd = $this->startDate - $tmpUnit;
        if ($tmpStart >= $this->globalStartDate or $this->mode == 4) {
          $this->previous = "report=" . $this->mode . "&startDate=" . $tmpStart . "&endDate=" . $tmpEnd;
        }

        $tmpStart = $this->endDate;
        $tmpEnd = $this->endDate + $tmpShift - 2 * $tmpUnit;
        if ($tmpEnd < mktime(0, 0, 0, date("m"), date("d"), date("Y"))) {
          $this->next = "report=" . $this->mode . "&startDate=" . $tmpStart . "&endDate=" . $tmpEnd;
        } else {
          if ($tmpEnd - $tmpDiff < mktime(0, 0, 0, date("m"), date("d"), date("Y"))) {
            $tmpEnd = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
            $this->next = "report=" . $this->mode . "&startDate=" . $tmpStart . "&endDate=" . $tmpEnd;
          }
        }
      }

      if (strlen($filter) > 0) {
        $tmp = "";
        $tmp1 = "";
        for ($i = 0; $i < $this->status_available_size; $i++) {
          if (substr($filter, $i, 1) == "1") {
            $tmp1 .= "1";
            if (strlen($tmp) == 0) {
              $tmp = "o.orders_status <> " . $this->status_available[$i]['index'];
            } else {
              $tmp .= " and o.orders_status <> " . $this->status_available[$i]['index'];
            }
          } else {
            $tmp1 .= "0";
          }
        }
        $this->filter_sql = $tmp;
        $this->filter = $tmp1;
      }
      $this->filter_link = "report=" . $this->mode . "&startDate=" . $startDate . "&endDate=" . $endDate;
        $this->query();
    }

    function query() {

      if (strlen($this->filter_sql) > 0) {
        $tmp_query = "SELECT sum(ot.value) as value, avg(ot.value) as avg, count(ot.value) as count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o WHERE ot.orders_id = o.orders_id and ot.class = 'ot_subtotal' and (" . $this->filter_sql . ")";
      } else {
        $tmp_query = "SELECT sum(ot.value) as value, avg(ot.value) as avg, count(ot.value) as count FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o WHERE ot.orders_id = o.orders_id and ot.class = 'ot_subtotal'";
      }

      for ($i = 0; $i < $this->size; $i++) {
        $report_query = os_db_query($tmp_query . " AND o.date_purchased >= '" . os_db_input(date("Y-m-d\TH:i:s", $this->startDates[$i])) . "' AND o.date_purchased < '" . os_db_input(date("Y-m-d\TH:i:s", $this->endDates[$i])) . "'");
        $report = os_db_fetch_array($report_query);
              $this->info[$i]['sum'] = $report['value'];
        $this->info[$i]['avg'] = $report['avg'];
        $this->info[$i]['count'] = $report['count'];
        switch ($this->mode) {
          case '1':
            $this->info[$i]['text'] = strftime("%H", $this->startDates[$i]) . " - " . strftime("%H", $this->endDates[$i]);
            $this->info[$i]['link'] = "";
            break;
          case '2':
            $this->info[$i]['text'] = strftime("%x", $this->startDates[$i]);
            $this->info[$i]['link'] = "report=1&startDate=" . $this->startDates[$i] . "&endDate=" . mktime(0, 0, 0, date("m", $this->endDates[$i]), date("d", $this->endDates[$i]) + 1, date("Y", $this->endDates[$i]));
            break;
          case '3':
            $this->info[$i]['text'] = strftime("%x", $this->startDates[$i]) . " - " . strftime("%x", mktime(0, 0, 0, date("m", $this->endDates[$i]), date("d", $this->endDates[$i]) - 1, date("Y", $this->endDates[$i])));
            $this->info[$i]['link'] = "report=2&startDate=" . $this->startDates[$i] . "&endDate=" . mktime(0, 0, 0, date("m", $this->endDates[$i]), date("d", $this->endDates[$i]) - 1, date("Y", $this->endDates[$i]));
            break;
          case '4':
            $this->info[$i]['text'] = strftime("%b %y", $this->startDates[$i]);
            $this->info[$i]['link'] = "report=3&startDate=" . $this->startDates[$i] . "&endDate=" . mktime(0, 0, 0, date("m", $this->endDates[$i]), date("d", $this->endDates[$i]) - 1, date("Y", $this->endDates[$i]));
            break;
          case '5':
            $this->info[$i]['text'] = date("Y", $this->startDates[$i]);
            $this->info[$i]['link'] = "report=4&startDate=" . $this->startDates[$i] . "&endDate=" . mktime(0, 0, 0, date("m", $this->endDates[$i]) - 1, date("d", $this->endDates[$i]), date("Y", $this->endDates[$i]));
            break;
        }
      }
      $tmp_query =  "select sum(ot.value) as shipping FROM " . TABLE_ORDERS_TOTAL . " ot, " . TABLE_ORDERS . " o WHERE ot.orders_id = o.orders_id and ot.class = 'ot_shipping'";

      for ($i = 0; $i < $this->size; $i++) {
        $report_query = os_db_query($tmp_query . " AND o.date_purchased >= '" . os_db_input(date("Y-m-d\TH:i:s", $this->startDates[$i])) . "' AND o.date_purchased < '" . os_db_input(date("Y-m-d\TH:i:s", $this->endDates[$i])) . "'");
        $report = os_db_fetch_array($report_query);
        $this->info[$i]['shipping'] = $report['shipping'];
      }
    }
  }
?>
