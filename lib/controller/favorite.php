<?php


namespace Izifir\Core\Controller;

use Bitrix\Main;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Izifir\Core\App;
use Izifir\Core\Models\FavoriteTable;

class Favorite extends Controller
{
    public function configureActions(): array
    {
        return [
            'add' => ['prefilters' => []],
            'delete' => ['prefilters' => []],
        ];
    }

    /**
     * @param int $elementId
     * @param null $userId
     * @return string[]
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     */
    public function addAction(int $elementId, $userId = null): array
    {
        global $USER;
        Loader::includeModule('izifir.core');

        if (!$userId)
            $userId = $USER->GetID();

        $result = ['status' => 'error', 'message' => 'Ошибка при добавлении в избранное'];

        if (!empty($userId)) {
            $fav = $this->getFavorite($elementId, $userId);
            if (!$fav) {
                $res = FavoriteTable::add([
                    'USER_ID' => $userId,
                    'ELEMENT_ID' => $elementId
                ]);
                if ($res->isSuccess()) {
                    $_SESSION[App::SESSION_FAVORITE_NAME][$elementId] = $elementId;
                    $result['status'] = 'success';
                    $result['message'] = 'Элемент успешно добавлен в избранное';
                    $result['count'] = count($_SESSION[App::SESSION_FAVORITE_NAME]);
                }
            }
        }

        return $result;
    }

    /**
     * @param int $elementId
     * @param null $userId
     * @throws Main\ArgumentException
     * @throws Main\LoaderException
     * @return array
     */
    public function deleteAction(int $elementId, $userId = null): array
    {
        global $USER;
        Loader::includeModule('izifir.core');

        if (!$userId)
            $userId = $USER->GetID();

        $result = ['status' => 'error', 'message' => 'Ошибка при удалении из избранного'];

        if (!empty($userId)) {
            $fav = $this->getFavorite($elementId, $userId);
            if ($fav) {
                $res = FavoriteTable::delete($fav['ID']);
                if ($res->isSuccess()) {
                    unset($_SESSION[App::SESSION_FAVORITE_NAME][$elementId]);
                    $result['status'] = 'success';
                    $result['message'] = 'Элемент успешно удален из избранного';
                    $result['count'] = count($_SESSION[App::SESSION_FAVORITE_NAME]);
                }
            }
        }

        return $result;
    }

    /**
     * @param int $elementId
     * @param int $userId
     * @return array|false
     * @throws Main\ArgumentException
     */
    protected function getFavorite(int $elementId, int $userId)
    {
        return FavoriteTable::getList([
            'filter' => [
                'USER_ID' => $userId,
                'ELEMENT_ID' => $elementId
             ]
        ])->fetch();
    }
}
