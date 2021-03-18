<div>
    Отчет о проделанной работе сотрудников за <?= (date('H') == 0 && date('i') == 0) ? (new DateTime(date('Y-m-d')))->modify('-1 day')->format('d.m.Y') : date('d.m.Y')?> г.
</div>
