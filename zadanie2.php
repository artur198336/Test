<?php

// Параметры будем брать из POST-запроса (по отправке формы)
if (!isset($_POST['n']) && !isset($_POST['m'])) {
// если переменные не заданы - выведем форму для ввода n и m
    print_form();
    return;
} else {
    // если переменные заданы (пришел POST-запрос) то проверяем их и делаем сортировку
    $fatals = $_POST['n'];
    $warnings = $_POST['m'];

    // проверяем - если в post-запросе переменные не были заданы - выведем
    // форму для n и m.
    if (($fatals == '') || ($warnings == '')) {
        print_form();
        return;
    }

    // Ok, теперь точно переменные заданы. Считаем коммиты.
    // Идея в чем - должно быть четное количество фатальных ошибок и 0 ворнингов, чтобы исправились все ошибки.
    // При этом, если изначально 0 ворнингов и нечетное количество фатальных ошибок задано - то исправить код невозможно.
    // в остальных случаях - мы можем создавать новые ворнинги и конвертировать их в фатальные ошибки столько раз, сколько
    // нужно, чтобы фатальных ошибок стало четное количество, а ворнингов 0.

    if (is_done($fatals, $warnings)) {
        echo ('Код чист');
        show_repeat();
    }

    if (!is_even($fatals) && ($warnings == 0)) {
        echo ('-1: код невозможно исправить');
        show_repeat();
    }

    $commits = 0; // число коммитов будем считать
    // решаем задачу как - генерируем нужное количество предупреждений,
    // убираем четные фатальные ошибки.
    // работаем в цикле пока все ошибки/предупреждения не исправлены
    while (!is_done($fatals, $warnings)) {
        echo ('Коммитов: ' . $commits . ', fatals: ' . $fatals . ', warnings: ' . $warnings . '</br>');

	// если предупреждений больше двух - фиксим 2 предупреждения
        if ($warnings > 2) {
            fix_2warnings($fatals, $warnings);
            $commits++;
            continue;
        } elseif ($warnings == 2) 
	// а если предупреждений всего 2 - смотрим, если нечетное количество фатальных ошибок - фиксим 2 предупреждения
	{
            if (!is_even($fatals)) {
                fix_2warnings($fatals, $warnings);
                $commits++;
                continue;
            }
        }

	// если четное количество фатальных ошибок и их больше двух или две - фиксим 2 фатальные ошибки
        
        if (is_even($fatals) && ($fatals >= 2)) {
            fix_2fatals($fatals);
            $commits++;
            continue;
        }

	// если же не отработало ни одно из условий выше - мы точно знаем что есть хотя бы 1 предупреждение.
	// фиксим его, чтобы получить 2 предупреждения

        fix_warning($warnings);
        $commits++;
    }
    
    echo ('<h3>Результат</h3>Коммитов: ' . $commits . ', fatals: ' . $fatals . ', warnings: ' . $warnings . '</br>');
    show_repeat();
}

// Функция показа формы для ввода параметров
function print_form() {

    echo '<form action="zadanie2.php" method="post">
          <label for="n">Введите кол-во фатальных ошибок N (от 0 до 1000):</label>
          <input type="number" id="n" name="n" min="0" max="1000"><br/>
          <label for="k">Введите кол-во предупреждений M (от 0 до 1000):</label>
          <input type="number" id="m" name="m" min="0" max="1000"><br/>
          <input type="submit" value="Отправить">
          </form>';
}

// проверка чет-нечет
function is_even($num) {
    if (($num % 2) == 0)
        return true;
    else
        return false;
}

// проверяем - закончили или нет
function is_done($fatals, $warnings) {
    if (($fatals == 0) && ($warnings == 0))
        return true;
    else
        return false;
}

function fix_warning(&$num) {
    $num += 1; // фикс 1 предупреждения генерирует 2
    echo (" => фикс 1 предупреждения за коммит<br/>");
}

function fix_2warnings(&$fatals, &$warnings) {
    if ($warnings < 2) {
        echo ('Некорректный вызов fix_2warnings -  недостаточно предупреждений для фикса' );
        return;
    }
    $warnings -= 2;
    $fatals++; // фикс 2 предупреждений генерирует 1 fatal
    echo (" => фикс 2 предупреждений за коммит<br/>");
}

function fix_2fatals(&$fatals) {
    if ($fatals < 2) {
        echo ('Некорректный вызов fix_2fatals - недостаточно фатальных ошибок для фикса');
        return;
    }
    $fatals -= 2;
    echo (" => фикс 2 фатальных ошибок за коммит<br/>");
    return;
}

function show_repeat() {
    // Выведем ссылку на повтор и выйдем
    echo '</br></br><a href="zadanie2.php">Повторить</a>';
    exit();
}
