<?php

use app\models\User;
use yii\helpers\Html;

$this->registerCssFile('@web/css/methodological-guide-style.css');

?>

<?php if (!User::isUserAdmin(Yii::$app->user->identity['username'])) : ?>

    <div class="methodological-guide">

        <div class="container-list">

            <h3><span class="bold">Шаг 2. Подготовка списка вопросов</span></h3>

            <div class="simple-block">
                <p>
                    <span>Задача:</span>
                    Сформулировать список вопросов, ответы на которые максимально помогают выявить сильные / слабые стороны MVP и положительную или отрицательную реакцию аудитории на предложенное MVP.
                </p>
                <p>
                    <span>Результат:</span>
                    Заполненная форма (шаг 2, Этап 8) на платформе Spaccel.ru
                </p>
            </div>

            <div class="bold">Посмотрите рекомендации по составлению вопросов, данные на предыдущих этапах.</div>

            <p>
                Вопросы должны касаться: вписывается ли ваш MVP в формат деятельности респондентов, насколько понятен материал, что понравилось, что не понравилось, чего не хватает в продукте,
                что неудобно по сравнению с теми продуктами, которыми они пользуются сейчас, какие-то пожелания. Спросите, какие важные аспекты в вашем проекте продукта не затронуты, которые
                следовало бы продумать. Стоит поинтересоваться, какова может быть цена решения, по мнению респондентов, есть ли у них намерение купить данный продукт (даже представленный MVP)
                по предложенной цене. Если среди аудитории найдутся желающие купить продукт после определенных доработок, то это стоит оформить протоколом или соглашением о намерениях.
            </p>

        </div>

    </div>

<?php else : ?>

    <div class="methodological-guide">

        <h3 class="header-text"><span>Этап 8. Подтверждение MVP</span></h3>

        <div class="container-list">

            <h3><span class="bold">Шаг 2. Подготовка списка вопросов</span></h3>

            <div class="simple-block">
                <p>
                    <span>Задача:</span>
                    Проверить на соответствие рекомендациям и формату заполненную форму <span>Сформировать список вопросов.</span>
                </p>
                <p>
                    <span>Результат:</span>
                    Информация проверена. При необходимости сформированы замечания о необходимости произвести корректировки.
                </p>
            </div>

            <div class="bold">Рекомендации и точки контроля:</div>
            <div class="container-text">
                <ul>
                    <li class="pl-15">Вопросы должны содержать выяснение принадлежности к сегменту и должны быть отмечены специальным признаком (инструмент spaccel). Эти вопросы могут быть идентичные предыдущим этапам;</li>
                    <li class="pl-15">Желательно, чтобы в вопросах запрашивалась информация о респонденте (1-2 вопроса) и о его клиентах (потребителях результатов его деятельности);</li>
                    <li class="pl-15">Должны быть вопросы, соответствующие теме встречи и рекомендациям.</li>
                </ul>
            </div>

            <h4><span class="bold"><u>Информация, полученная Проектантом:</u></span></h4>

            <div class="simple-block">
                <p>
                    <span>Задача:</span>
                    Сформулировать список вопросов, ответы на которые максимально помогают выявить сильные / слабые стороны MVP и положительную или отрицательную реакцию аудитории на предложенное MVP.
                </p>
                <p>
                    <span>Результат:</span>
                    Заполненная форма (шаг 2, Этап 8) на платформе Spaccel.ru
                </p>
            </div>

            <div class="bold">Посмотрите рекомендации по составлению вопросов, данные на предыдущих этапах.</div>

            <p>
                Вопросы должны касаться: вписывается ли ваш MVP в формат деятельности респондентов, насколько понятен материал, что понравилось, что не понравилось, чего не хватает в продукте,
                что неудобно по сравнению с теми продуктами, которыми они пользуются сейчас, какие-то пожелания. Спросите, какие важные аспекты в вашем проекте продукта не затронуты, которые
                следовало бы продумать. Стоит поинтересоваться, какова может быть цена решения, по мнению респондентов, есть ли у них намерение купить данный продукт (даже представленный MVP)
                по предложенной цене. Если среди аудитории найдутся желающие купить продукт после определенных доработок, то это стоит оформить протоколом или соглашением о намерениях.
            </p>

        </div>

    </div>

<?php endif; ?>

<div class="row">
    <div class="col-md-12" style="display:flex;justify-content: center;">
        <?= Html::button('Закрыть', [
            'onclick' => 'return $(".modal_instruction_page").modal("hide");',
            'class' => 'btn btn-default',
            'style' => [
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'center',
                'background' => '#F5A4A4',
                'color' => '#ffffff',
                'width' => '180px',
                'height' => '40px',
                'font-size' => '16px',
                'text-transform' => 'uppercase',
                'font-weight' => '700',
                'padding-top' => '9px',
                'border-radius' => '8px',
                'margin-top' => '28px'
            ]
        ]) ?>
    </div>
</div>
