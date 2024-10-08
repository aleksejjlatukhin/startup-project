<?php

use app\models\ConversationAdmin;
use app\models\ConversationDevelopment;
use app\models\forms\FormCreateMessageAdmin;
use app\models\User;
use app\modules\admin\models\form\SearchForm;
use app\modules\contractor\models\ConversationContractor;
use app\modules\expert\models\ConversationExpert;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\MessageAdmin;
use yii\widgets\LinkPager;

$this->title = 'Сообщения';
$this->registerCssFile('@web/css/message-view.css');

/**
 * @var ConversationAdmin $conversation
 * @var FormCreateMessageAdmin $formMessage
 * @var User $user
 * @var User $admin
 * @var SearchForm $searchForm
 * @var MessageAdmin[] $messages
 * @var int $countMessages
 * @var Pagination $pagesMessages
 * @var User $development
 * @var ConversationExpert[] $expertConversations
 * @var ConversationContractor[] $contractorConversations
 * @var ConversationDevelopment $conversation_development
 */

?>

<div class="message-view">

    <!--Preloader begin-->
    <div id="preloader">
        <div id="cont">
            <div class="round"></div>
            <div class="round"></div>
            <div class="round"></div>
            <div class="round"></div>
        </div>
        <div id="loading">Loading</div>
    </div>
    <!--Preloader end-->

    <div class="row profile_menu">

<!--        <div class="link_open_and_close_menu_profile">Открыть меню профиля</div>-->
<!---->
<!--        --><?php //echo Html::a('Данные пользователя', ['/profile/index', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Сводные таблицы', ['/profile/result', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Трэкшн карты', ['/profile/roadmap', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Протоколы', ['/profile/report', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>
<!---->
<!--        --><?php //echo Html::a('Презентации', ['/profile/presentation', 'id' => $user->getId()], [
//            'class' => 'link_in_the_header',
//        ]) ?>

    </div>

    <div class="row">
        <div class="col-sm-6 col-lg-4 hide_block_menu_profile">

<!--            --><?php //echo Html::a('Данные пользователя', ['/profile/index', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Сводные таблицы', ['/profile/result', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Трэкшн карты', ['/profile/roadmap', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Протоколы', ['/profile/report', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>
<!---->
<!--            --><?php //echo Html::a('Презентации', ['/profile/presentation', 'id' => $user->getId()], [
//                'class' => 'link_in_the_header',
//            ]) ?>

        </div>
    </div>

    <div class="row all_content_messages">

        <div class="col-sm-6 col-lg-4 conversation-list-menu">

            <div id="conversation-list-menu">

                <!--Блок беседы с трекером и техподдержкой-->
                <div class="containerAdminConversation">

                    <div class="container-user_messages active-message" id="adminConversation-<?= $conversation->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($admin->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от трекера-->
                        <?php if ($user->countUnreadMessagesFromAdmin) : ?>
                            <div class="countUnreadMessagesSender active"><?= $user->countUnreadMessagesFromAdmin ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($admin->checkOnline === true) : ?>
                            <div class="checkStatusOnlineUser active"></div>
                        <?php else : ?>
                            <div class="checkStatusOnlineUser"></div>
                        <?php endif; ?>

                        <div class="container_user_messages_text_content">

                            <div class="row block_top">

                                <div class="col-xs-8">Трекер</div>

                                <div class="col-xs-4 text-right">
                                    <?php if ($conversation->lastMessage) : ?>
                                        <?= date('d.m.y H:i', $conversation->lastMessage->getCreatedAt()) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($conversation->lastMessage) : ?>
                                <div class="block_bottom_exist_message">

                                    <?php if ($conversation->lastMessage->sender->getAvatarImage()) : ?>
                                        <?= Html::img('@web/upload/user-'.$conversation->lastMessage->getSenderId().'/avatar/'.$conversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                    <?php else : ?>
                                        <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($conversation->lastMessage->getDescription()) : ?>
                                            <?= $conversation->lastMessage->getDescription() ?>
                                        <?php else : ?>
                                            ...
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="block_bottom_not_exist_message">Нет сообщений</div>
                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="container-user_messages" id="conversationTechnicalSupport-<?= $conversation_development->getId() ?>">

                        <!--Проверка существования аватарки-->
                        <?php if ($development->getAvatarImage()) : ?>
                            <?= Html::img('@web/upload/user-'.$development->getId().'/avatar/'.$development->getAvatarImage(), ['class' => 'user_picture']) ?>
                        <?php else : ?>
                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                        <?php endif; ?>

                        <!--Кол-во непрочитанных сообщений от Техподдержки-->
                        <?php if ($user->countUnreadMessagesFromDev) : ?>
                            <div class="countUnreadMessagesSender active"><?= $user->countUnreadMessagesFromDev ?></div>
                        <?php else : ?>
                            <div class="countUnreadMessagesSender"></div>
                        <?php endif; ?>

                        <!--Проверка онлайн статуса-->
                        <?php if ($development->checkOnline === true) : ?>
                            <div class="checkStatusOnlineUser active"></div>
                        <?php else : ?>
                            <div class="checkStatusOnlineUser"></div>
                        <?php endif; ?>

                        <div class="container_user_messages_text_content">

                            <div class="row block_top">

                                <div class="col-xs-8">Техническая поддержка</div>

                                <div class="col-xs-4 text-right">
                                    <?php if ($conversation_development->lastMessage) : ?>
                                        <?= date('d.m.y H:i', $conversation_development->lastMessage->getCreatedAt()) ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($conversation_development->lastMessage) : ?>
                                <div class="block_bottom_exist_message">

                                    <?php if ($conversation_development->lastMessage->sender->getAvatarImage()) : ?>
                                        <?= Html::img('@web/upload/user-'.$conversation_development->lastMessage->getSenderId().'/avatar/'.$conversation_development->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                    <?php else : ?>
                                        <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                    <?php endif; ?>

                                    <div>
                                        <?php if ($conversation_development->lastMessage->getDescription()) : ?>
                                            <?= $conversation_development->lastMessage->getDescription() ?>
                                        <?php else : ?>
                                            ...
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="block_bottom_not_exist_message">Нет сообщений</div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                <!--Блок бесед с экспертами-->
                <div class="containerExpertConversations">

                    <div class="title_block_conversation">
                        <div class="title">Эксперты</div>
                    </div>

                    <?php if ($expertConversations) : ?>

                        <?php foreach ($expertConversations as $oneConversation) : ?>

                            <div class="container-user_messages" id="expertConversation-<?= $oneConversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($oneConversation->expert->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$oneConversation->getExpertId().'/avatar/'.$oneConversation->expert->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от эксперта-->
                                <?php if ($user->getCountUnreadMessagesExpertFromUser($oneConversation->getExpertId())) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $user->getCountUnreadMessagesExpertFromUser($oneConversation->getExpertId()) ?></div>
                                <?php else : ?>
                                    <div class="countUnreadMessagesSender"></div>
                                <?php endif; ?>

                                <!--Проверка онлайн статуса-->
                                <?php if ($oneConversation->expert->checkOnline === true) : ?>
                                    <div class="checkStatusOnlineUser active"></div>
                                <?php else : ?>
                                    <div class="checkStatusOnlineUser"></div>
                                <?php endif; ?>

                                <div class="container_user_messages_text_content">

                                    <div class="row block_top">

                                        <div class="col-xs-8"><?= $oneConversation->expert->getUsername() ?></div>

                                        <div class="col-xs-4 text-right">
                                            <?php if ($oneConversation->lastMessage) : ?>
                                                <?= date('d.m.y H:i', $oneConversation->lastMessage->getCreatedAt()) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($oneConversation->lastMessage) : ?>
                                        <div class="block_bottom_exist_message">

                                            <?php if ($oneConversation->lastMessage->sender->getAvatarImage()) : ?>
                                                <?= Html::img('@web/upload/user-'.$oneConversation->lastMessage->getSenderId().'/avatar/'.$oneConversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                            <?php else : ?>
                                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                            <?php endif; ?>

                                            <div>
                                                <?php if ($oneConversation->lastMessage->getDescription()) : ?>
                                                    <?= $oneConversation->lastMessage->getDescription() ?>
                                                <?php else : ?>
                                                    ...
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="text-center block_not_conversations">Нет экспертов</div>

                    <?php endif; ?>

                </div>

                <!--Блок бесед с исполнителями-->
                <div class="containerContractorConversations">

                    <div class="title_block_conversation">
                        <div class="title">Исполнители</div>
                    </div>

                    <?php if ($contractorConversations) : ?>

                        <?php foreach ($contractorConversations as $oneConversation) : ?>

                            <div class="container-user_messages" id="contractorConversation-<?= $oneConversation->getId() ?>">

                                <!--Проверка существования аватарки-->
                                <?php if ($oneConversation->contractor->getAvatarImage()) : ?>
                                    <?= Html::img('@web/upload/user-'.$oneConversation->getContractorId().'/avatar/'.$oneConversation->contractor->getAvatarImage(), ['class' => 'user_picture']) ?>
                                <?php else : ?>
                                    <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default']) ?>
                                <?php endif; ?>

                                <!--Кол-во непрочитанных сообщений от исполнителя-->
                                <?php if ($user->getCountUnreadMessagesContractorFromUser($oneConversation->getContractorId())) : ?>
                                    <div class="countUnreadMessagesSender active"><?= $user->getCountUnreadMessagesContractorFromUser($oneConversation->getContractorId()) ?></div>
                                <?php else : ?>
                                    <div class="countUnreadMessagesSender"></div>
                                <?php endif; ?>

                                <!--Проверка онлайн статуса-->
                                <?php if ($oneConversation->contractor->checkOnline === true) : ?>
                                    <div class="checkStatusOnlineUser active"></div>
                                <?php else : ?>
                                    <div class="checkStatusOnlineUser"></div>
                                <?php endif; ?>

                                <div class="container_user_messages_text_content">

                                    <div class="row block_top">

                                        <div class="col-xs-8"><?= $oneConversation->contractor->getUsername() ?></div>

                                        <div class="col-xs-4 text-right">
                                            <?php if ($oneConversation->lastMessage) : ?>
                                                <?= date('d.m.y H:i', $oneConversation->lastMessage->getCreatedAt()) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <?php if ($oneConversation->lastMessage) : ?>
                                        <div class="block_bottom_exist_message">

                                            <?php if ($oneConversation->lastMessage->sender->getAvatarImage()) : ?>
                                                <?= Html::img('@web/upload/user-'.$oneConversation->lastMessage->getSenderId().'/avatar/'.$oneConversation->lastMessage->sender->getAvatarImage(), ['class' => 'icon_sender_last_message']) ?>
                                            <?php else : ?>
                                                <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'icon_sender_last_message_default']) ?>
                                            <?php endif; ?>

                                            <div>
                                                <?php if ($oneConversation->lastMessage->getDescription()) : ?>
                                                    <?= $oneConversation->lastMessage->getDescription() ?>
                                                <?php else : ?>
                                                    ...
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div class="block_bottom_not_exist_message">Нет сообщений</div>
                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    <?php else : ?>

                        <div class="text-center block_not_conversations">Нет исполнителей</div>

                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-8">

            <div class="button_open_close_list_users" style="">Открыть список пользователей</div>

            <div class="chat">

                <?php if ($messages) : ?>

                    <div class="data-chat" id="data-chat">

                        <?php if ($countMessages > $pagesMessages->pageSize) : ?>

                            <div class="pagination-messages">
                                <?= LinkPager::widget([
                                    'pagination' => $pagesMessages,
                                    'activePageCssClass' => 'pagination_active_page',
                                    'options' => ['class' => 'messages-pagination-list pagination'],
                                    'maxButtonCount' => 1,
                                ]) ?>
                            </div>

                            <div class="text-center block_for_link_next_page_masseges">
                                <?= Html::a('Посмотреть предыдущие сообщения', ['#'], ['class' => 'button_next_page_masseges'])?>
                            </div>

                        <?php endif; ?>

                        <?php $totalDateMessages = array(); // Массив общих дат сообщений ?>

                        <?php foreach ($messages as $i => $message) : ?>

                            <?php
                            // Вывод общих дат для сообщений
                            if (!in_array($message->dayAndDateRus, $totalDateMessages, false)) {
                                $totalDateMessages[] = $message->dayAndDateRus;
                                echo '<div class="dayAndDayMessage">'.$message->dayAndDateRus.'</div>';
                            }
                            ?>

                            <?php if ($message->getSenderId() !== $user->getId()) : ?>

                                <?php if ($message->getStatus() === MessageAdmin::NO_READ_MESSAGE) : ?>

                                    <div class="message addressee-user unreadmessage" id="message_id-<?= $message->getId() ?>">

                                        <?php if ($admin->getAvatarImage()) : ?>
                                            <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                                        <?php else : ?>
                                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                                        <?php endif; ?>

                                        <div class="sender_data">
                                            <div class="sender_info">
                                                <div>Администратор</div>
                                                <div>
                                                    <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                                                    <?= date('H:i', $message->getCreatedAt()) ?>
                                                </div>
                                            </div>

                                            <div class="message-description">

                                                <?php if ($message->getDescription()) : ?>
                                                    <?= $message->getDescription() ?>
                                                <?php endif; ?>

                                                <?php if ($message->files) : ?>
                                                    <div class="message-description-files">
                                                    <?php foreach ($message->files as $file) : ?>
                                                        <div>
                                                            <?= Html::a($file->getFileName(), ['/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>

                                    </div>

                                <?php else : ?>

                                    <div class="message addressee-user" id="message_id-<?= $message->getId() ?>">

                                        <?php if ($admin->getAvatarImage()) : ?>
                                            <?= Html::img('@web/upload/user-'.$admin->getId().'/avatar/'.$admin->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                                        <?php else : ?>
                                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                                        <?php endif; ?>

                                        <div class="sender_data">
                                            <div class="sender_info">
                                                <div>Администратор</div>
                                                <div>
                                                    <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                                                    <?= date('H:i', $message->getCreatedAt()) ?>
                                                </div>
                                            </div>

                                            <div class="message-description">

                                                <?php if ($message->getDescription()) : ?>
                                                    <?= $message->getDescription() ?>
                                                <?php endif; ?>

                                                <?php if ($message->files) : ?>
                                                    <div class="message-description-files">
                                                        <?php foreach ($message->files as $file) : ?>
                                                            <div>
                                                                <?= Html::a($file->getFileName(), ['/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>

                                    </div>

                                <?php endif; ?>

                            <?php else : ?>

                                <?php if ($message->getStatus() === MessageAdmin::NO_READ_MESSAGE) : ?>

                                    <div class="message addressee-admin unreadmessage" id="message_id-<?= $message->getId() ?>">

                                        <?php if ($user->getAvatarImage()) : ?>
                                            <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                                        <?php else : ?>
                                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                                        <?php endif; ?>

                                        <div class="sender_data">
                                            <div class="sender_info">
                                                <div class="interlocutor"><?= $user->getUsername() ?></div>
                                                <div>
                                                    <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                                                    <?= date('H:i', $message->getCreatedAt()) ?>
                                                </div>
                                            </div>

                                            <div class="message-description">

                                                <?php if ($message->getDescription()) : ?>
                                                    <?= $message->getDescription() ?>
                                                <?php endif; ?>

                                                <?php if ($message->files) : ?>
                                                    <div class="message-description-files">
                                                        <?php foreach ($message->files as $file) : ?>
                                                            <div>
                                                                <?= Html::a($file->getFileName(), ['/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>

                                    </div>

                                <?php else : ?>

                                    <div class="message addressee-admin" id="message_id-<?= $message->getId() ?>">

                                        <?php if ($user->getAvatarImage()) : ?>
                                            <?= Html::img('@web/upload/user-'.$user->getId().'/avatar/'.$user->getAvatarImage(), ['class' => 'user_picture_message']) ?>
                                        <?php else : ?>
                                            <?= Html::img('/images/icons/button_user_menu.png', ['class' => 'user_picture_default_message']) ?>
                                        <?php endif; ?>

                                        <div class="sender_data">
                                            <div class="sender_info">
                                                <div class="interlocutor"><?= $user->getUsername() ?></div>
                                                <div>
                                                    <?= Html::img('/images/icons/icon_double_check.png', ['class' => 'icon_read_message']) ?>
                                                    <?= date('H:i', $message->getCreatedAt()) ?>
                                                </div>
                                            </div>

                                            <div class="message-description">

                                                <?php if ($message->getDescription()) : ?>
                                                    <?= $message->getDescription() ?>
                                                <?php endif; ?>

                                                <?php if ($message->files) : ?>
                                                    <div class="message-description-files">
                                                        <?php foreach ($message->files as $file) : ?>
                                                            <div>
                                                                <?= Html::a($file->getFileName(), ['/message/download', 'category' => $file->getCategory(), 'id' => $file->getId()], ['target' => '_blank', 'title' => $file->getFileName()]) ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </div>

                                    </div>

                                <?php endif; ?>

                            <?php endif; ?>

                        <?php endforeach; ?>

                    </div>

                    <div class="create-message">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'create-message-admin',
                            'action' => Url::to(['/message/send-message', 'id' => Yii::$app->request->get('id')]),
                            'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]);
                        ?>

                        <div class="form-send-email">

                            <?= $form->field($formMessage, 'description')->label(false)->textarea([
                                'id' => 'input_send_message',
                                'rows' => 1,
                                'maxlength' => true,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Напишите ваше сообщение',
                                'autocomplete' => 'off'
                            ]) ?>

                            <?= $form->field($formMessage, 'message_files[]', ['template' => "{label}\n{input}"])->fileInput(['id' => 'input_message_files', 'multiple' => true, 'accept' => 'text/plain, application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, image/x-png, image/jpeg'])->label(false) ?>

                            <?= Html::submitButton('Отправить', ['id' =>  'submit_send_message']) ?>

                            <?= Html::img('/images/icons/send_email_button.png', ['class' => 'send_message_button', 'title' => 'Отправить сообщение']) ?>

                            <?= Html::img('/images/icons/button_attach_files.png', ['class' => 'attach_files_button', 'title' => 'Прикрепить файлы']) ?>

                        </div>

                        <?php ActiveForm::end(); ?>

                        <!--Сюда загружаем названия загруженных файлов или сообшение о превышении кол-ва файлов-->
                        <div class="block_attach_files"></div>
                    </div>


                <?php else : // Если отсутствуют сообщения ?>

                    <div class="data-chat" id="data-chat">
                        <div class="block_not_exist_message">
                            У Вас нет пока общих сообщений с данным пользователем...
                        </div>
                    </div>

                    <div class="create-message">

                        <?php
                        $form = ActiveForm::begin([
                            'id' => 'create-message-admin',
                            'action' => Url::to(['/message/send-message', 'id' => Yii::$app->request->get('id')]),
                            'options' => ['enctype' => 'multipart/form-data', 'class' => 'g-py-15'],
                            'errorCssClass' => 'u-has-error-v1',
                            'successCssClass' => 'u-has-success-v1-1',
                        ]);
                        ?>

                        <div class="form-send-email">

                            <?= $form->field($formMessage, 'description')->label(false)->textarea([
                                'id' => 'input_send_message',
                                'rows' => 1,
                                'maxlength' => true,
                                'required' => true,
                                'class' => 'style_form_field_respond form-control',
                                'placeholder' => 'Напишите ваше сообщение',
                                'autocomplete' => 'off'
                            ]) ?>

                            <?= $form->field($formMessage, 'message_files[]', ['template' => "{label}\n{input}"])->fileInput(['id' => 'input_message_files', 'multiple' => true, 'accept' => 'text/plain, application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, image/x-png, image/jpeg'])->label(false) ?>

                            <?= Html::submitButton('Отправить', ['id' =>  'submit_send_message']) ?>

                            <?= Html::img('/images/icons/send_email_button.png', ['class' => 'send_message_button', 'title' => 'Отправить сообщение']) ?>

                            <?= Html::img('/images/icons/button_attach_files.png', ['class' => 'attach_files_button', 'title' => 'Прикрепить файлы']) ?>

                        </div>

                        <?php ActiveForm::end(); ?>

                        <!--Сюда загружаем названия загруженных файлов или сообшение о превышении кол-ва файлов-->
                        <div class="block_attach_files"></div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!--Подключение скриптов-->
<?php $this->registerJsFile('@web/js/message_view.js'); ?>
<?php $this->registerJsFile('@web/js/form_message_admin.js'); ?>
