<?php
/*
 * @Author: Kolegov Vladislav
 * @Date: 2019-09-26 13:46:56
 * @Last Modified by: Kolegov Vladislav
 * @Last Modified time: 2020-01-26 11:52:50
 */

namespace VKolegov\VKAPIWrapper;

use GuzzleHttp\Client as HttpClient;

class VkClientWrapper
{
    const AD_FORMAT_OPTIONS = [
        // 1 => "Изображение и текст",
        // 2 => "Большое изображение",
        // 4 => "Продвижение сообществ или приложений, квадратное изображение",
        // 8 => "Специальный формат сообществ",
        9 => "Запись в сообществе",
        // 11 => "Адаптивный формат",
    ];

    const COST_TYPES = [
        [
            'val' => 0,
            'title' => 'CPC (Оплата за переходы)',
        ],
        [
            'val' => 1,
            'title' => 'CPM (Оплата за 1000 показов)',
        ],
        // [
        //     'val' => 3,
        //     'title' => 'OCPM (Оптимизированная оплата за показы)',
        // ],
    ];

    const SEXES = [
        [
            'val' => 0,
            'title' => 'Любой',
        ],
        [
            'val' => 1,
            'title' => 'Женский',
        ],
        [
            'val' => 2,
            'title' => 'Мужской',
        ],
    ];

    const RETARGETING_EVENTS = [
        [
            'val' => 1,
            'title' => 'Просмотр промопоста',
        ],
        [
            'val' => 2,
            'title' => 'Переход по ссылке или переход в сообщество',
        ],
        [
            'val' => 3,
            'title' => 'Переход в сообщество',
        ],
        [
            'val' => 4,
            'title' => 'Подписка на сообщество',
        ],
        [
            'val' => 5,
            'title' => 'Отписка от новостей сообщества',
        ],
        [
            'val' => 6,
            'title' => 'Скрытие или жалоба',
        ],
        [
            'val' => 10,
            'title' => 'Запуск видео',
        ],
        [
            'val' => 11,
            'title' => 'Досмотр видео до 3с',
        ],
        [
            'val' => 12,
            'title' => 'Досмотр видео до 25%',
        ],
        [
            'val' => 13,
            'title' => 'Досмотр видео до 50%',
        ],
        [
            'val' => 14,
            'title' => 'Досмотр видео до 75%',
        ],
        [
            'val' => 15,
            'title' => 'Досмотр видео до 100%',
        ],
        [
            'val' => 20,
            'title' => 'Лайк продвигаемой записи',
        ],
        [
            'val' => 21,
            'title' => 'Репост продвигаемой записи',
        ],
    ];

    const PRETTY_CARDS_BUTTONS = [
        ['val' => 'app_join', 'title' => 'Запустить'],
        ['val' => 'app_game_join', 'title' => 'Играть'],
        ['val' => 'open_url', 'title' => 'Перейти'],
        ['val' => 'open', 'title' => 'Открыть'],
        ['val' => 'more', 'title' => 'Подробнее'],
        ['val' => 'call', 'title' => 'Позвонить'],
        ['val' => 'book', 'title' => 'Забронировать'],
        ['val' => 'enroll', 'title' => 'Записаться'],
        ['val' => 'register', 'title' => 'Зарегистрироваться'],
        ['val' => 'buy', 'title' => 'Купить'],
        ['val' => 'buy_ticket', 'title' => 'Купить билет'],
        ['val' => 'to_shop', 'title' => 'В магазин'],
        ['val' => 'order', 'title' => 'Заказать'],
        ['val' => 'create', 'title' => 'Создать'],
        ['val' => 'install', 'title' => 'Установить'],
        ['val' => 'contact', 'title' => 'Связаться'],
        ['val' => 'fill', 'title' => 'Заполнить'],
        ['val' => 'choose', 'title' => 'Выбрать'],
        ['val' => 'try', 'title' => 'Попробовать'],
        ['val' => 'join_public', 'title' => 'Подписаться'],
        ['val' => 'join_event', 'title' => 'Я пойду'],
        ['val' => 'join_group', 'title' => 'Вступить'],
        ['val' => 'im_group', 'title' => 'Связаться'],
        ['val' => 'im_group2', 'title' => 'Написать'],
        ['val' => 'begin', 'title' => 'Начать'],
        ['val' => 'get', 'title' => 'Получить'],
    ];

    /**
     * @var VKolegov\VKAPIWrapper\VkClientWrapperCore
     */
    private $core;

    public function __construct($access_token, $version = 5.94)
    {
        $this->core = new VkClientWrapperCore($access_token, $version);
    }

    public function getAppPermissions($user_id)
    {
        $params = [
            'user_id' => $user_id,
        ];

        $response = $this->core->call('account.getAppPermissions', $params);

        return $response;
    }

    public function getUsersInfo($user_ids)
    {
        $params = [
            'user_ids' => join(",", $user_ids), // TODO: Не больше тысячи
        ];

        $response = $this->core->call('users.get', $params);

        if (is_string($response)) {
            return $response;
        }

        return $response;
    }

    public function getAgencyClients($account_id)
    {
        $params = array(
            'account_id' => $account_id,
        );

        $response = $this->core->call('ads.getClients', $params);

        return $response;
    }

    public function getAdsCampaigns($account_id, $client_id = null, $ids = null)
    {
        $params = array(
            'account_id' => $account_id,
        );

        if (isset($client_id)) {
            $params['client_id'] = $client_id;
        }

        if (is_array($ids)) {
            $params['campaign_ids'] = json_encode($ids);
        }

        $response = $this->core->call('ads.getCampaigns', $params);

        if ($response == 100) {
            $params['client_id'] = $this->getAgencyClients($account_id)[0]['id'];

            $response = $this->core->call('ads.getCampaigns', $params);
        }

        return $response;
    }

    /**
     * Возвращает список аудиторий ретаргетинга
     *
     * @param int $client_id [Только для рекламных агентств] id клиента, в рекламном кабинете которого находятся аудитории
     * @param int $extended если 1, в результатах будет указан код для размещения на сайте. Устаревший параметр. Используется только для старых групп ретаргетинга, которые пополнялись без помощи пикселя. Для новых аудиторий его следует опускать.
     *
     * @return array Массив с тематиками объявлений и их описанием
     */
    public function getAdsTargetGroups($account_id, $client_id = null, $extended = 0)
    {
        $params = array(
            'account_id' => $account_id,
            'extended' => $extended,
        );

        if (isset($client_id)) {
            $params['client_id'] = $client_id;
        }

        $response = $this->core->call('ads.getTargetGroups', $params);

        if ($response == 100) {
            $params['client_id'] = $this->getAgencyClients($account_id)[0]['id'];

            $response = $this->core->call('ads.getTargetGroups', $params);
        }

        return $response;
    }

    /**
     * Возвращает URL-адрес для загрузки фотографии рекламного объявления
     *
     * @param integer $ad_format Формат изображения
     * @param integer $icon 1 — получить URL для загрузки логотипа, а не основного изображения. Используется только для ad_format = 11.
     * @return string|array
     */
    public function getAdsUploadURL($ad_format, $icon = null)
    {
        $params = [
            'ad_format' => $ad_format,
        ];

        // Валидация
        if ($ad_format == 11 && in_array($icon, [0, 1])) {
            $params['icon'] = $icon;
        }

        $response = $this->core->call('ads.getUploadURL', $params);

        return $response;
    }

    /**
     * Загружает изображение для рекламы по указанному пути с помощью указанного URL
     *
     * @param string $file_path Путь к изображению
     * @param integer $ad_format Формат рекламы
     * @return array Результат загрузки
     */
    // TODO: Поддержка типа объявления 11
    public function uploadPhotoForAds($file_path, $ad_format)
    {
        $upload_url = $this->getAdsUploadURL($ad_format);

        \Log::info($upload_url);
        \Log::info($file_path);

        if (!is_string($upload_url)) {
            return $upload_url;
        }

        $params = [
            // 'file' => new \CURLFile($file_path, mime_content_type($file_path), 'filename_post.png'),
            // 'file' => fopen($file_path, 'r'),
            // 'file' => file_get_contents($file_path),
        ];

        return $this->uploadImage($file_path, $upload_url);
    }

    public function uploadImage($filepath, $upload_url)
    {
        $params = [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($filepath, 'rb'),
                ],
            ],
        ];

        // \Log::info($params);

        // Запрос
        $http_client = new HttpClient();

        $http_response = $http_client->post($upload_url, $params);

        // $raw_response = performPostRequest($upload_url, $params);

        // $response = json_decode($raw_response, true);

        \Log::info($http_response->getBody());

        $response = json_decode($http_response->getBody(), true);

        if (isset($response['errcode'])) {
            \Log::error("[uploadPhotoForAds()] Ошибка при загрузке изображения: " . $response['errcode']);
            return $response['errcode'];
        }

        if (isset($response['photo'])) {
            return $response['photo'];
        }

        return $response;
    }

    /**
     * Запрашиваем посты со стены группы или пользователя
     *
     * @param integer $owner_id ID группы или пользователя
     * @param integer $count Сколько постов
     * @param integer $offset Смещение относительно последнего поста на стене
     * @param boolean $is_group Если true - группа
     * @return array|string Массив с информацией о постах или сообщение об ошибке
     */

    public function getPosts($owner_id, $count = 10, $offset = 0, $is_group = false)
    {

        if ($count < 0 || $count > 100) {
            return null;
        }

        $params = [
            'owner_id' => $owner_id,
            'count' => $count,
            'offset' => $offset,
            'extended' => false,
        ];

        if ($is_group === true) {
            $params['owner_id'] = "-" . $owner_id;
        }

        $response = $this->core->call('wall.get', $params);

        if (is_string($response)) {
            return $response;
        }

        return $response['items'];
    }

    /**
     * Запрашиваем информацию о лайках (до 1000)
     *
     * @param string $type post|comment|photo|audio|etc...
     * @param integer $owner_id идентификатор владельца Like-объекта: id пользователя, id сообщества или id приложения.
     * @param integer $item_id идентификатор Like-объекта. Если type равен sitepage, то параметр item_id может содержать значение параметра page_id, используемый при инициализации виджета «Мне нравится».
     * @param boolean $is_group является ли owner группой
     * @return array|string Информация о лайках или строка с описанием ошибки
     */
    public function getLikes($type, $owner_id, $item_id, $is_group = false)
    {
        $params = [
            'type' => $type,
            'owner_id' => $owner_id,
            'item_id' => $item_id,
            'count' => 1000,
            'offset' => 0,
            'extended' => 1,
            'friends_only' => 0,
        ];

        if ($is_group === true) {
            $params['owner_id'] = "-" . $owner_id;
        }

        $response = $this->core->call('likes.getList', $params);

        if (is_string($response)) {
            return $response;
        }

        return $response['items'];
    }

    /**
     * Запрашиваем информацию о лайках (до 12000)
     *
     * @param string $type post|comment|photo|audio|etc...
     * @param integer $owner_id идентификатор владельца Like-объекта: id пользователя, id сообщества или id приложения.
     * @param integer $item_id идентификатор Like-объекта. Если type равен sitepage, то параметр item_id может содержать значение параметра page_id, используемый при инициализации виджета «Мне нравится».
     * @param boolean $is_group является ли owner группой
     * @return array|string Информация о лайках или строка с описанием ошибки
     */
    public function getLikesAtLeast($type, $owner_id, $item_id, $offset = 0, $at_least = 12000, $is_group = false)
    {
        $script_parameters = [
            'type' => $type,
            'owner_id' => $owner_id,
            'item_id' => $item_id,
            'initial_offset' => $offset,
            'iterations' => ceil($at_least / 1000),
        ];

        if ($is_group === true) {
            $script_parameters['owner_id'] = "-" . $owner_id;
        }

        $response = $this->core->callScript(__DIR__ . '/scripts/get_all_likes.vks', $script_parameters);

        return $response;
    }

    /**
     * Запрашиваем информацию о всех лайках
     *
     * @param string $type post|comment|photo|audio|etc...
     * @param integer $owner_id идентификатор владельца Like-объекта: id пользователя, id сообщества или id приложения.
     * @param integer $item_id идентификатор Like-объекта. Если type равен sitepage, то параметр item_id может содержать значение параметра page_id, используемый при инициализации виджета «Мне нравится».
     * @param boolean $is_group является ли owner группой
     * @return array|string Информация о лайках или строка с описанием ошибки
     */
    public function getAllLikesInfo($type, $owner_id, $item_id, $is_group = false)
    {
        $likes_info = [];

        $offset = 0;
        $at_least_per_call = 12000;

        while (true && $at_least_per_call > 0) {

            $likes_info_part = $this->getLikesAtLeast($type, $owner_id, $item_id, $offset, $at_least_per_call, $is_group);

            if (!is_array($likes_info_part)) {
                $at_least_per_call -= 1000; // Если не удалось - уменьшаем минимум на тысячу
                continue;
            }
            $likes_info = array_merge($likes_info, $likes_info_part);

            if (count($likes_info_part) == 0) {
                break;
            }

            if (count($likes_info_part) < $at_least_per_call) {
                break;
            }

            $offset += $at_least_per_call;
        }

        return $likes_info;
    }

    /**
     * Запрашивает комментарии к посту (до 100)
     *
     * @param integer $owner_id ID группы или пользователя
     * @param integer $post_id ID поста
     * @param integer $comment_id ID комментария, под которым ветка обсуждения
     * @param boolean $is_group является ли owner группой
     * @return array|string Массив с информацией о комментариях либо строка с описанием ошибки
     */
    public function getPostComments($owner_id, $post_id, $comment_id = null, $offset = 0, $is_group = false)
    {
        $params = [
            'owner_id' => $owner_id,
            'post_id' => $post_id,
            'thread_items_count' => 10,
            'count' => 100,
            'offset' => $offset,
        ];

        if ($is_group === true) {
            $params['owner_id'] = '-' . $owner_id;
        }

        if (isset($comment_id)) {
            $params['comment_id'] = $comment_id;
        }

        $response = $this->core->call('wall.getComments', $params);

        if (is_string($response)) {
            return $response;
        }

        return $response['items'];
    }

    public function getPostCommentsAll_2($owner_id, $post_id, $comment_id = null, $is_group = false)
    {

        $post_comments = [];
        $total_comments = 0;
        $offset = 0;

        while (true) {
            $post_comments_part = $this->getPostComments($owner_id, $post_id, $comment_id, $offset, $is_group);

            $retrived_comments_count = count($post_comments_part);

            if (!is_array($post_comments_part) || $retrived_comments_count < 1) {
                break;
            }

            $total_comments += $retrived_comments_count;

            // \Log::debug('Получено ' . $retrived_comments_count . ' комментов');

            foreach ($post_comments_part as &$post_comment) {

                if (isset($post_comment['thread'])) {

                    if ($post_comment['thread']['count'] > 10) {
                        $thread_comments = $this->getPostCommentsAll_2($owner_id, $post_id, $post_comment['id'], $is_group);

                        $retrived_thread_comments_count = count($thread_comments);

                        // \Log::debug('Получено ' . $retrived_thread_comments_count . ' тред-комментов');
                        $total_comments += $retrived_thread_comments_count;

                        $post_comment['thread']['items'] = $thread_comments;
                    } else {
                        $total_comments += $post_comment['thread']['count'];
                    }
                }

            }

            $post_comments = array_merge($post_comments, $post_comments_part);

            if ($post_comments_part < 100) {
                break;
            }

            usleep(50000);
            $offset += 100;
        }

        \Log::debug("В общем получено " . $total_comments . " комментариев для " . $owner_id . "_" . $post_id);
        return $post_comments;
    }

    /**
     * Запрашивает комментарии к посту (до 2500)
     *
     * @param integer $owner_id ID группы или пользователя
     * @param integer $post_id ID поста
     * @param integer $comment_id ID комментария, под которым ветка обсуждения
     * @param boolean $is_group является ли owner группой
     * @return array|string Массив с информацией о комментариях либо строка с описанием ошибки
     */

    public function getPostCommentsAtLeast($owner_id, $post_id, $comment_id = null, $offset = 0, $at_least = 2500, $is_group = false)
    {
        $script_parameters = [
            'owner_id' => $owner_id,
            'post_id' => $post_id,
            'initial_offset' => $offset,
            'iterations' => ceil($at_least / 100),
        ];

        if ($is_group === true) {
            $script_parameters['owner_id'] = "-" . $owner_id;
        }

        $response = $this->core->callScript(__DIR__ . '/scripts/get_all_comments.vks', $script_parameters);

        return $response;
    }

    public function getAllReposts($owner_id, $post_id, $is_group = false)
    {
        $reposts = [
            'items' => [],
            'profiles' => [],
            'groups' => [],
        ];
        $offset = 0;
        $total_reposts = 0;

        while (true) {
            $reposts_part = $this->getReposts($owner_id, $post_id, $offset, $is_group);

            $retrieved_reposts_count = count($reposts_part['items']);

            if (!is_array($reposts_part) || $retrieved_reposts_count < 1) {
                break;
            }
            $total_reposts += $retrieved_reposts_count;

            \Log::debug('Получено ' . $retrieved_reposts_count . ' репостов');

            $reposts['items'] = array_merge($reposts['items'], $reposts_part['items']);
            $reposts['profiles'] = array_merge($reposts['profiles'], $reposts_part['profiles']);
            $reposts['groups'] = array_merge($reposts['groups'], $reposts_part['groups']);

            if ($retrieved_reposts_count < 1000) {
                break;
            }

            usleep(50000);
            $offset += 1000;
        }

        \Log::debug("В общем получено " . $total_reposts . " репостов");
        return $reposts;
    }

    public function getReposts($owner_id, $post_id, $offset = 0, $is_group = false)
    {
        $params = [
            'owner_id' => $owner_id,
            'post_id' => $post_id,
            'offset' => $offset,
            'count' => 1000,
        ];

        if ($is_group === true) {
            $params['owner_id'] = '-' . $owner_id;
        }

        $response = $this->core->call('wall.getReposts', $params);

        if (is_string($response)) {
            return $response;
        }

        return $response;
    }

    /**
     * Создает скрытую запись, которая не попадает на стену сообщества
     * и в дальнейшем может быть использована для создания рекламного объявления типа "Запись в сообществе".
     *
     * @param integer $group_id ID группы
     * @param string $text Текст сообщения
     * @param array $attachments Вложения
     * @param array $additional_params Дополнительные параметры запроса
     * @return null|int Возвращает ID созданного поста в случае успеха
     */
    public function postAdsStealth($group_id, $text = null, $attachments = null, $additional_params = null)
    {
        $params = array(
            'owner_id' => '-' . $group_id,
            'signed' => 0,
        );

        // Цепляем или нет сообщение
        if (!is_string($text)) {
            \Log::warning("[postAdsStealth()] \$text не является строкой");
        } else {
            $params['message'] = $text;
        }

        // Цепляем или нет вложения
        if (!is_array($attachments) || empty($attachments)) {
            \Log::info("[postAdsStealth()] \$attachments не является массивом или он пустой");
            if (is_string($attachments)) {
                \Log::warning("[postAdsStealth()] \$attachments является строкой");
                $params['attachments'] = $attachments;
            }
        } else {
            $params['attachments'] = implode(",", $attachments); // photo100172_166443618,photo-1_265827614
        }

        if (!isset($params['message']) && !isset($params['attachments'])) {
            \Log::error("[postAdsStealth()] Не установлены основные параметры");
            return null;
        }

        // Цепляем или нет доп. параметры
        if (!isset($additional_params) ||
            !is_array($additional_params) ||
            empty($additional_params)) {
            \Log::info("[postAdsStealth()] \$additional_params не является массивом или он пустой");
        } else {
            foreach ($additional_params as $key => $value) {
                $params[$key] = $value;
            }
        }

        \Log::notice("[postAdsStealth()] Текст: " . $text);

        $response = $this->core->call('wall.postAdsStealth', $params);

        if (is_string($response)) {
            return $response;
        }

        // Возвращаем ID созданного поста
        return intval($response['post_id']);
    }

    public function createAds($account_id, $data)
    {
        $params = array(
            'account_id' => $account_id,
            'data' => $data,
        );

        $response = $this->core->call('ads.createAds', $params);

        \Log::info("[VKApiHelper][createAds()] Ответы: " . var_export($response, true));

        return $response;
    }

    /**
     * Редактирует скрытую рекламную запись (не попадает на стену сообщества)
     * используется для рекламного объявления типа "Запись в сообществе".
     *
     * @param integer $group_id ID группы
     * @param string $text Текст сообщения
     * @param array $attachments Вложения
     * @param array $additional_params Дополнительные параметры запроса
     * @return null|int Возвращает 1 в случае успеха
     */
    public function editPostAdsStealth($group_id, $post_id, $message = null, $attachments = null, $additional_params = null)
    {
        $params = array(
            'owner_id' => '-' . $group_id,
            'post_id' => $post_id,
            'signed' => 0,
        );

        // Цепляем или нет сообщение
        if (!is_string($message)) {
            \Log::warning("[VKApiHelper][editPostAdsStealth()] \$message не является строкой");
        } else {
            $params['message'] = $message;
        }

        // Цепляем или нет вложения
        if (!is_array($attachments) || empty($attachments)) {
            \Log::warning("[VKApiHelper][editPostAdsStealth()] \$attachments не является массивом или он пустой");
            if (is_string($attachments)) {
                \Log::warning("[VKApiHelper][editPostAdsStealth()] \$attachments является строкой");
                $params['attachments'] = $attachments;
            }
        } else {
            $params['attachments'] = implode(",", $attachments); // photo100172_166443618,photo-1_265827614
        }

        if (!isset($params['message']) && !isset($params['attachments'])) {
            \Log::error("[VKApiHelper][editPostAdsStealth()] Не установлены основные параметры");
            return null;
        }

        // Цепляем или нет доп. параметры
        if (!isset($additional_params) ||
            !is_array($additional_params) ||
            empty($additional_params)) {
            \Log::warning("[VKApiHelper][editPostAdsStealth()] \$additional_params не является массивом или он пустой");
        } else {
            foreach ($additional_params as $key => $value) {
                $params[$key] = $value;
            }
        }

        \Log::info("[VKApiHelper][editPostAdsStealth()] Текст: " . $message);

        $response = $this->core->call('wall.editAdsStealth', $params);

        \Log::info("[VKApiHelper][editPostAdsStealth()] Ответ: " . var_export($response, true));

        // Возвращаем
        if ($response == 1) {
            return true;
        } else {
            return $response;
        }
    }

    public function getAds($account_id, $client_id = null, $ids = null)
    {
        $params = [
            'account_id' => $account_id,
        ];

        if ($client_id !== null) {
            $params['client_id'] = $client_id;
        }

        if (is_array($ids)) {
            $params['ad_ids'] = json_encode($ids);
        }

        $response = $this->core->call('ads.getAds', $params);

        if ($response == 100) {
            $params['client_id'] = $this->getAgencyClients($account_id)[0]['id'];

            $response = $this->core->call('ads.getAds', $params);
        }

        return $response;
    }

    public function updateAds($account_id, $data)
    {
        $params = [
            'account_id' => $account_id,
        ];

        if (!is_array($data)) {
            return null;
        }

        if (defined('PHP_MAJOR_VERSION') && PHP_MAJOR_VERSION >= 7 && defined('PHP_MINOR_VERSION') && PHP_MINOR_VERSION >= 1) {
            $json_flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS;
        } else {
            $json_flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        }

        $data = json_encode($data, $json_flags);

        $params['data'] = $data;

        $response = $this->core->call('ads.updateAds', $params);

        return $response;
    }

    public function getAdsStatistics($account_id, $ids_type, $ids, $start_date, $end_date)
    {
        $params = [
            'account_id' => $account_id,
            'ids_type' => $ids_type,
            'ids' => join(",", $ids),
            'period' => 'day',
            'date_from' => $start_date,
            'date_to' => $end_date,
        ];

        $response = $this->core->call('ads.getStatistics', $params);

        return $response;
    }

    public function getAdsTargeting($account_id, $client_id = null, $ads_id)
    {
        $params = array(
            'account_id' => $account_id,
        );

        if (isset($client_id)) {
            $params['client_id'] = $client_id;
        }

        if (is_array($ads_id)) {
            $params['ad_ids'] = json_encode($ads_id);
        }

        $response = $this->core->call('ads.getAdsTargeting', $params);

        if ($response == 100) {
            $params['client_id'] = $this->getAgencyClients($account_id)[0]['id'];

            $response = $this->core->call('ads.getAdsTargeting', $params);
        }

        return $response;
    }

    //
    // Pretty cards
    //

    public function getPrettyCardUploadURL()
    {
        return $this->core->call('prettyCards.getUploadURL', []);
    }

    public function createPrettyCard($owner_id, $photo, string $title, string $link, $button = null, $price = null, $price_old = null)
    {
        $params = [
            'owner_id' => $owner_id,
            'photo' => $photo,
            'title' => $title,
            'link' => $link,
        ];

        if (array_key_exists($button, self::PRETTY_CARDS_BUTTONS)) {
            $params['button'] = $button;
        }

        if ($price >= 0) {
            $params['price'] = $price;

            if ($price_old >= 0) {
                $params['price_old'] = $price_old;
            }
        }

        $response = $this->core->call('prettyCards.create', $params);

        return $response;
    }

    public function editPrettyCard($owner_id, $card_id, $pretty_card_data)
    {
        $params = [
            'owner_id' => $owner_id,
            'card_id' => $card_id,
        ];

        foreach ($pretty_card_data as $pretty_card_param_name => $pretty_card_param) {
            $params[$pretty_card_param_name] = $pretty_card_param;
        }

        $response = $this->core->call('prettyCards.edit', $params);

        return $response;
    }

}
