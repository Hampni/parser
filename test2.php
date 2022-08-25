<?php
// Обязательно
declare(ticks = 1);

// функция обработки сигнала
function sig_handler($signo)
{

    switch ($signo) {
        case SIGTERM:
            // Обработка задач остановки
            exit;
        case SIGHUP:
            // обработка задач перезапуска
            break;
        case SIGUSR1:
            echo "Получен сигнал SIGUSR1...\n";
            break;
        default:
            // Обработка других сигналов
    }

}

echo "Установка обработчиков сигналов...\n";

// Установка обработчиков сигналов
pcntl_signal(SIGTERM, "sig_handler");
pcntl_signal(SIGHUP,  "sig_handler");
pcntl_signal(SIGUSR1, "sig_handler");

// или можете использовать объект
// pcntl_signal(SIGUSR1, array($obj, "do_something"));

echo "Отправка сигнала SIGUSR1 себе...\n";

// Отправка SIGUSR1 процессу с текущим id (т.е. себе)
// для использования функций posix_* требуется модуль posix
posix_kill(posix_getpid(), SIGUSR1);

echo "Завершено\n";

?>
