<?php
//DATES WITHOUT DATEPICKER.  MUST BE ENTERED: [DD]-[MM]-[YYYY].  REQUIRED FOR PRODUCTION AND COURSE.
//ADD/EDIT FORMS:-
<input type="text">
//NOT
<input type="date">

//VALIDATION:-
    if (!preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/", $prod_first_perf_date))
    {
      $errors['prod_first_perf_date'] = '**You must enter a valid First Performance date in the prescribed format.**';
      $prod_first_perf_date = NULL;
    }
    else
    {
      list($prod_first_perf_date_DD, $prod_first_perf_date_MM, $prod_first_perf_date_YYYY) = explode('-', $prod_first_perf_date);

      if (!checkdate((int)$prod_first_perf_date_MM, (int)$prod_first_perf_date_DD, (int)$prod_first_perf_date_YYYY))
      {
        $errors['prod_first_perf_date'] = '**You must enter a valid First Performance date.**';
        $prod_first_perf_date = NULL;
      }
      else
      {
        $prod_first_perf_date = $prod_first_perf_date_YYYY.$prod_first_perf_date_MM.$prod_first_perf_date_DD;
      }
    }

    if (!empty($prod_press_perf_date))
    {
      if (!preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/", $prod_press_perf_date))
      {
        $errors['prod_press_perf_date'] = '**You must enter a valid Press Performance date in the prescribed format or leave empty.**';
        $prod_press_perf_date = NULL;
      }
      else
      {
        list($prod_press_perf_date_DD, $prod_press_perf_date_MM, $prod_press_perf_date_YYYY) = explode('-', $prod_press_perf_date);

        if (!checkdate((int)$prod_press_perf_date_MM, (int)$prod_press_perf_date_DD, (int)$prod_press_perf_date_YYYY))
        {
          $errors['prod_press_perf_date'] = '**You must enter a valid Press Performance date or leave empty.**';
          $prod_press_perf_date = NULL;
        }
        else
        {
          $prod_press_perf_date = $prod_press_perf_date_YYYY.$prod_press_perf_date_MM.$prod_press_perf_date_DD;

          if ($prod_press_perf_date_tbc == '1')
          {
            $errors['prod_press_perf_date_tbc'] = '**Press Performance date must be left empty if this box is checked.**';
          }
        }
      }
    }
    else
    {
      $prod_press_perf_date = NULL;
    }

    if (!preg_match("/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/", $prod_last_perf_date))
    {
      $errors['prod_last_perf_date'] = '**You must enter a valid Last Performance date in the prescribed format.**';
      $prod_last_perf_date = NULL;
    }
    else
    {
      list($prod_last_perf_date_DD, $prod_last_perf_date_MM, $prod_last_perf_date_YYYY) = explode('-', $prod_last_perf_date);

      if (!checkdate((int)$prod_last_perf_date_MM, (int)$prod_last_perf_date_DD, (int)$prod_last_perf_date_YYYY))
      {
        $errors['prod_last_perf_date'] = '**You must enter a valid Last Performance date.**';
        $prod_last_perf_date = NULL;
      }
      else
      {
        $prod_last_perf_date = $prod_last_perf_date_YYYY.$prod_last_perf_date_MM.$prod_last_perf_date_DD;
      }
    }

    if (!is_null($prod_first_perf_date) && !is_null($prod_last_perf_date) && $prod_first_perf_date > $prod_last_perf_date)
    {
      $errors['prod_first_perf_date'] = '**Must be lower than or equal to Last Performance date.**';
      $errors['prod_last_perf_date'] = '**Must be higher than or equal to First Performance date.**';
    }

    if (!is_null($prod_first_perf_date) && !is_null($prod_press_perf_date) && !is_null($prod_last_perf_date) && ($prod_press_perf_date < $prod_first_perf_date || $prod_press_perf_date > $prod_last_perf_date))
    {
      $errors['prod_press_perf_date'] = '**Must be higher than or equal to First Performance and lower than or equal to Last Performance.**';
    }

//SELECTION FROM DB TO POPULATE EDIT PAGE FIELDS
    $prod_first_perf_date = preg_replace("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", "$3-$2-$1" , $row['prod_first_perf_date']);
    if (empty($row['prod_press_perf_date']))
    { $prod_press_perf_date = ''; }
    else
    { $prod_press_perf_date = preg_replace("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", "$3-$2-$1" , $row['prod_press_perf_date']); }
    $prod_last_perf_date = preg_replace("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", "$3-$2-$1" , $row['prod_last_perf_date']);
?>