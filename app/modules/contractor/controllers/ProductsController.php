<?php

namespace app\modules\contractor\controllers;

use app\models\ClientSettings;
use app\models\ConfirmGcp;
use app\models\ConfirmProblem;
use app\models\ConfirmSegment;
use app\models\ContractorTaskProducts;
use app\models\ContractorTasks;
use app\models\ContractorTaskSimilarProductParams;
use app\models\ContractorTaskSimilarProducts;
use app\models\Gcps;
use app\models\PatternHttpException;
use app\models\Problems;
use app\models\Segments;
use app\models\StatusConfirmHypothesis;
use app\models\User;
use app\modules\contractor\models\form\ContractorTaskSimilarProductForm;
use scotthuangzl\export2excel\DownloadAction;
use scotthuangzl\export2excel\Export2ExcelBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ErrorAction;
use yii\web\HttpException;
use yii\web\Response;

class ProductsController extends AppContractorController
{
    /**
     * @param $action
     * @return bool
     * @throws HttpException
     */
    public function beforeAction($action): bool
    {
        $currentUser = User::findOne(Yii::$app->user->getId());
        $currentClientUser = $currentUser->clientUser;

        if (in_array($action->id, ['export', 'export-similar-products'], true)) {

            $task = ContractorTasks::findOne((int)Yii::$app->request->get('taskId'));
            if (!$task) {
                PatternHttpException::noData();
            }

            $contractor = User::findOne($task->getContractorId());
            if (!$contractor) {
                PatternHttpException::noData();
            }

            if (User::isUserContractor($currentUser->getUsername()) && $task->getContractorId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserSimple($currentUser->getUsername()) && $task->project->getUserId() === $currentUser->getId()) {
                return parent::beforeAction($action);
            }

            if (User::isUserMainAdmin($currentUser->getUsername()) || User::isUserDev($currentUser->getUsername()) || User::isUserAdminCompany($currentUser->getUsername())) {

                $modelClientUser = $contractor->clientUser;

                if ($currentClientUser->getClientId() === $modelClientUser->getClientId()) {
                    return parent::beforeAction($action);
                }

                if ($modelClientUser->client->settings->getAccessAdmin() === ClientSettings::ACCESS_ADMIN_TRUE && !User::isUserAdminCompany($currentUser->getUsername())) {
                    return parent::beforeAction($action);
                }
            }

            PatternHttpException::noAccess();

        } else {
            return parent::beforeAction($action);
        }
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'download' => [
                'class' => DownloadAction::class,
            ],
        ];
    }


    /**
     * Экспорт отчета по продуктам
     * https://www.yiiframework.com/extension/yii2-export2excel
     *
     * @param int $taskId
     * @return void
     */
    public function actionExport(int $taskId): void
    {
        $data = ContractorTaskProducts::find()
            ->select(['name', 'price', 'satisfaction', 'flaws', 'advantages', 'suppliers'])
            ->andWhere(['task_id' => $taskId])
            ->asArray()
            ->all();

        foreach ($data as $i => $item) {
            if ($item['satisfaction'] === (string)ContractorTaskProducts::SATISFACTION_LOW) {
                $data[$i]['satisfaction'] = 'Низкая';
            } elseif ($item['satisfaction'] === (string)ContractorTaskProducts::SATISFACTION_MIDDLE) {
                $data[$i]['satisfaction'] = 'Средняя';
            } elseif ($item['satisfaction'] === (string)ContractorTaskProducts::SATISFACTION_HIGH) {
                $data[$i]['satisfaction'] = 'Высокая';
            } else {
                $data[$i]['satisfaction'] = '';
            }
        }

        $excel_data = Export2ExcelBehavior::excelDataFormat($data);
        $excel_title = ['Наименование', 'Цена продукта', 'Удовлетворенность продуктом', 'Недостатки продукта', 'Преимущества продукта', 'Ключевые поставщики'];
        $excel_ceils = $excel_data['excel_ceils'];
        $excel_content = array(
            array(
                'sheet_name' => 'Список',
                'sheet_title' => $excel_title,
                'ceils' => $excel_ceils,
                'freezePane' => 'B2',
                'headerColor' => Export2ExcelBehavior::getCssClass("header"),
                'headerColumnCssClass' => array(
                    'Status_Description' => Export2ExcelBehavior::getCssClass('grey'),
                ),
                'oddCssClass' => Export2ExcelBehavior::getCssClass("odd"),
                'evenCssClass' => Export2ExcelBehavior::getCssClass("even"),
            )
        );
        $excel_file = "ProductReport";
        $this->export2excel($excel_content, $excel_file);
    }


    /**
     * Экспорт отчета по продуктам аналогам
     * https://www.yiiframework.com/extension/yii2-export2excel
     *
     * @param int $taskId
     * @return void
     */
    public function actionExportSimilarProducts(int $taskId): void
    {
        /** @var ContractorTaskSimilarProducts[] $products */
        $products = ContractorTaskSimilarProducts::find()
            ->select(['name', 'ownership_cost', 'price', 'params'])
            ->andWhere(['task_id' => $taskId])
            ->all();

        $productParams = ContractorTaskSimilarProductParams::findAll(['deleted_at' => null, 'task_id' => $taskId]);
        $productParams = ArrayHelper::map($productParams, 'id', 'name');
        $excel_title = ['Наименование', 'Стоимость владения продуктом', 'Цена продукта'];

        foreach ($productParams as $param) {
            $excel_title[] = $param;
        }

        $data = [];
        foreach ($products as $i => $product) {
            $data[$i]['name'] = $product->getName();
            $data[$i]['ownership_cost'] = $product->getOwnershipCost();
            $data[$i]['price'] = $product->getPrice();
            foreach ($productParams as $keyParam => $param) {
                $data[$i][$param] = $product->getParams()[$keyParam];
            }
        }

        $excel_data = Export2ExcelBehavior::excelDataFormat($data);
        $excel_ceils = $excel_data['excel_ceils'];
        $excel_content = array(
            array(
                'sheet_name' => 'Список',
                'sheet_title' => $excel_title,
                'ceils' => $excel_ceils,
                'freezePane' => 'B2',
                'headerColor' => Export2ExcelBehavior::getCssClass("header"),
                'headerColumnCssClass' => array(
                    'Status_Description' => Export2ExcelBehavior::getCssClass('grey'),
                ),
                'oddCssClass' => Export2ExcelBehavior::getCssClass("odd"),
                'evenCssClass' => Export2ExcelBehavior::getCssClass("even"),
            )
        );
        $excel_file = "SimilarProductReport";
        $this->export2excel($excel_content, $excel_file);
    }


    /**
     * @return array|false
     */
    public function actionGetDataCreateForm(int $taskId)
    {
        if(Yii::$app->request->isAjax) {

            $response = ['renderAjax' => $this->renderAjax('form', [
                'model' => new ContractorTaskProducts(),
                'action' => 'create', 'taskId' => $taskId,
                'productId' => null])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $taskId
     * @param bool $isMobile
     * @return array|false
     * @throws \Throwable
     */
    public function actionCreate(int $taskId, bool $isMobile = false)
    {
        if(Yii::$app->request->isAjax) {

            $model = new ContractorTaskProducts();
            $model->setContractorId(Yii::$app->user->getId());
            $model->setTaskId($taskId);
            $task = ContractorTasks::findOne($taskId);

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {

                    if ($task->getStatus() === ContractorTasks::TASK_STATUS_NEW) {
                        $task->changeStatus(ContractorTasks::TASK_STATUS_PROCESS);
                    }

                    $response = ['renderAjax' => $this->renderAjax('product_list', [
                        'models' => $model->task->products, 'allowEdit' => true, 'isMobile' => $isMobile])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $taskId
     * @param bool $isMobile
     * @return array|false
     */
    public function actionGetProductList(int $taskId, bool $isMobile = false)
    {
        if(Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($taskId);
            $allowEdit = false;
            $hypothesisConfirm = $task->hypothesis;

            /** @var Segments|Problems|Gcps $hypothesis */
            if ($hypothesisConfirm instanceof ConfirmSegment) {
                $hypothesis = Segments::find(false)
                    ->andWhere(['id' => $hypothesisConfirm->getSegmentId()])
                    ->one();
            } elseif ($hypothesisConfirm instanceof ConfirmProblem) {
                $hypothesis = Problems::find(false)
                    ->andWhere(['id' => $hypothesisConfirm->getProblemId()])
                    ->one();
            } elseif ($hypothesisConfirm instanceof ConfirmGcp) {
                $hypothesis = Gcps::find(false)
                    ->andWhere(['id' => $hypothesisConfirm->getGcpId()])
                    ->one();
            }

            if (User::isUserContractor(Yii::$app->user->identity['username']) &&
                $hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
                in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) {
                $allowEdit = true;
            }
            $response = ['renderAjax' => $this->renderAjax('product_list', [
                'models' => $task->products, 'allowEdit' => $allowEdit, 'isMobile' => $isMobile])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @return array|false
     */
    public function actionGetDataUpdateForm(int $id)
    {
        if(Yii::$app->request->isAjax) {
            $model = ContractorTaskProducts::findOne($id);

            $response = ['renderAjax' => $this->renderAjax('form', [
                'model' => $model, 'action' => 'update',
                'taskId' => $model->getTaskId(),
                'productId' => $model->getId()])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @param bool $isMobile
     * @return array|false
     */
    public function actionUpdate(int $id, bool $isMobile = false)
    {
        if(Yii::$app->request->isAjax) {

            $model = ContractorTaskProducts::findOne($id);

            if ($model->load(Yii::$app->request->post())) {

                if ($model->save()) {

                    $response = ['renderAjax' => $this->renderAjax('product_list', [
                        'models' => $model->task->products, 'allowEdit' => true, 'isMobile' => $isMobile])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     * @throws \Throwable
     */
    public function actionDelete(int $id)
    {
        if(Yii::$app->request->isAjax) {

            try {
                $model = ContractorTaskProducts::findOne($id);
                $model->delete();
                $success = true;
            } catch (\Exception $exception) {
                $success = false;
            }

            $response = ['success' => $success];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @return array|false
     */
    public function actionGetSimilarParamList(int $taskId)
    {
        if(Yii::$app->request->isAjax) {

            $models = ContractorTaskSimilarProductParams::findAll(['task_id' => $taskId]);
            $newModel = new ContractorTaskSimilarProductParams();

            $response = ['renderAjax' => $this->renderAjax('similar_param_list', [
                'models' => $models, 'newModel' => $newModel, 'taskId' => $taskId])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $taskId
     * @return array|false
     */
    public function actionCreateSimilarParam(int $taskId)
    {
        if(Yii::$app->request->isAjax) {

            $model = new ContractorTaskSimilarProductParams();
            $model->setTaskId($taskId);

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {

                    $models = ContractorTaskSimilarProductParams::findAll(['task_id' => $taskId]);
                    $newModel = new ContractorTaskSimilarProductParams();

                    $response = ['renderAjax' => $this->renderAjax('similar_param_list', [
                        'models' => $models, 'newModel' => $newModel, 'taskId' => $taskId])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionUpdateSimilarParam(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = ContractorTaskSimilarProductParams::findOne($id);

            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {

                    $models = ContractorTaskSimilarProductParams::findAll(['task_id' => $model->getTaskId()]);
                    $newModel = new ContractorTaskSimilarProductParams();

                    $response = ['renderAjax' => $this->renderAjax('similar_param_list', [
                        'models' => $models, 'newModel' => $newModel, 'taskId' => $model->getTaskId()])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionDeleteSimilarParam(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = ContractorTaskSimilarProductParams::findOne($id);
            $model->setDeletedAt(time());

            if ($model->save()) {

                $models = ContractorTaskSimilarProductParams::findAll(['task_id' => $model->getTaskId()]);
                $newModel = new ContractorTaskSimilarProductParams();

                $response = ['renderAjax' => $this->renderAjax('similar_param_list', [
                    'models' => $models, 'newModel' => $newModel, 'taskId' => $model->getTaskId()])];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     */
    public function actionRecoverySimilarParam(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $model = ContractorTaskSimilarProductParams::findOne($id);
            $model->setDeletedAt(null);

            if ($model->save()) {

                $models = ContractorTaskSimilarProductParams::findAll(['task_id' => $model->getTaskId()]);
                $newModel = new ContractorTaskSimilarProductParams();

                $response = ['renderAjax' => $this->renderAjax('similar_param_list', [
                    'models' => $models, 'newModel' => $newModel, 'taskId' => $model->getTaskId()])];
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->response->data = $response;
                return $response;
            }
        }
        return false;
    }


    /**
     * @return array|false
     */
    public function actionGetDataCreateSimilarProductForm(int $taskId)
    {
        if(Yii::$app->request->isAjax) {

            $product = new ContractorTaskSimilarProducts();
            $product->setTaskId($taskId);
            $productParams = ContractorTaskSimilarProductParams::findAll(['deleted_at' => null, 'task_id' => $taskId]);
            $productParams = ArrayHelper::map($productParams, 'id', 'name');

            $response = ['renderAjax' => $this->renderAjax('similar_product_form', [
                'model' => new ContractorTaskSimilarProductForm($product),
                'action' => 'create', 'taskId' => $taskId,
                'productId' => null, 'productParams' => $productParams])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $taskId
     * @param bool $isMobile
     * @return array|false
     * @throws \Throwable
     */
    public function actionCreateSimilarProduct(int $taskId, bool $isMobile = false)
    {
        if(Yii::$app->request->isAjax) {

            if ($post = $_POST['ContractorTaskSimilarProductForm']) {
                $model = new ContractorTaskSimilarProducts();
                $model->setTaskId($taskId);
                $model->setContractorId(Yii::$app->user->getId());
                $model->setName($post['name']);
                $model->setOwnershipCost($post['ownership_cost']);
                $model->setPrice($post['price']);
                $model->setParams($post['params'] ?: []);

                if ($model->save()) {

                    $task = ContractorTasks::findOne($taskId);
                    $allowEdit = false;
                    $hypothesisConfirm = $task->hypothesis;

                    /** @var Problems|Gcps $hypothesis */
                    if ($hypothesisConfirm instanceof ConfirmProblem) {
                        $hypothesis = Problems::find(false)
                            ->andWhere(['id' => $hypothesisConfirm->getProblemId()])
                            ->one();
                    } elseif ($hypothesisConfirm instanceof ConfirmGcp) {
                        $hypothesis = Gcps::find(false)
                            ->andWhere(['id' => $hypothesisConfirm->getGcpId()])
                            ->one();
                    }

                    if (User::isUserContractor(Yii::$app->user->identity['username']) &&
                        $hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
                        in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) {
                        $allowEdit = true;
                    }

                    $productParams = ContractorTaskSimilarProductParams::findAll(['deleted_at' => null, 'task_id' => $taskId]);
                    $productParams = ArrayHelper::map($productParams, 'id', 'name');

                    $response = ['renderAjax' => $this->renderAjax('similar_product_list', [
                        'models' => $task->similarProducts, 'allowEdit' => $allowEdit,
                        'isMobile' => $isMobile, 'productParams' => $productParams, 'taskId' => $taskId])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $taskId
     * @param bool $isMobile
     * @return array|false
     */
    public function actionGetSimilarProductList(int $taskId, bool $isMobile = false)
    {
        if(Yii::$app->request->isAjax) {

            $task = ContractorTasks::findOne($taskId);
            $allowEdit = false;
            $hypothesisConfirm = $task->hypothesis;

            /** @var Problems|Gcps $hypothesis */
            if ($hypothesisConfirm instanceof ConfirmProblem) {
                $hypothesis = Problems::find(false)
                    ->andWhere(['id' => $hypothesisConfirm->getProblemId()])
                    ->one();
            } elseif ($hypothesisConfirm instanceof ConfirmGcp) {
                $hypothesis = Gcps::find(false)
                    ->andWhere(['id' => $hypothesisConfirm->getGcpId()])
                    ->one();
            }

            if (User::isUserContractor(Yii::$app->user->identity['username']) &&
                $hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
                in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) {
                $allowEdit = true;
            }

            $productParams = ContractorTaskSimilarProductParams::findAll(['deleted_at' => null, 'task_id' => $taskId]);
            $productParams = ArrayHelper::map($productParams, 'id', 'name');

            $response = ['renderAjax' => $this->renderAjax('similar_product_list', [
                'models' => $task->similarProducts, 'allowEdit' => $allowEdit,
                'isMobile' => $isMobile, 'productParams' => $productParams, 'taskId' => $taskId])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @return array|false
     */
    public function actionGetDataUpdateSimilarProductForm(int $id)
    {
        if(Yii::$app->request->isAjax) {

            $product = ContractorTaskSimilarProducts::findOne($id);
            $productParams = ContractorTaskSimilarProductParams::findAll(['deleted_at' => null, 'task_id' => $product->getTaskId()]);
            $productParams = ArrayHelper::map($productParams, 'id', 'name');

            $response = ['renderAjax' => $this->renderAjax('similar_product_form', [
                'model' => new ContractorTaskSimilarProductForm($product, 'update'),
                'action' => 'update', 'taskId' => $product->getTaskId(),
                'productId' => $id, 'productParams' => $productParams])];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }


    /**
     * @param int $id
     * @param bool $isMobile
     * @return array|false
     */
    public function actionUpdateSimilarProduct(int $id, bool $isMobile = false)
    {
        if(Yii::$app->request->isAjax) {

            if ($post = $_POST['ContractorTaskSimilarProductForm']) {
                $model = ContractorTaskSimilarProducts::findOne($id);
                $model->setName($post['name']);
                $model->setOwnershipCost($post['ownership_cost']);
                $model->setPrice($post['price']);
                $model->setParams($post['params'] ?: []);

                if ($model->save()) {

                    $task = ContractorTasks::findOne($model->getTaskId());
                    $allowEdit = false;
                    $hypothesisConfirm = $task->hypothesis;

                    /** @var Problems|Gcps $hypothesis */
                    if ($hypothesisConfirm instanceof ConfirmProblem) {
                        $hypothesis = Problems::find(false)
                            ->andWhere(['id' => $hypothesisConfirm->getProblemId()])
                            ->one();
                    } elseif ($hypothesisConfirm instanceof ConfirmGcp) {
                        $hypothesis = Gcps::find(false)
                            ->andWhere(['id' => $hypothesisConfirm->getGcpId()])
                            ->one();
                    }

                    if (User::isUserContractor(Yii::$app->user->identity['username']) &&
                        $hypothesis->getExistConfirm() === StatusConfirmHypothesis::MISSING_OR_INCOMPLETE &&
                        in_array($task->getStatus(), [ContractorTasks::TASK_STATUS_NEW, ContractorTasks::TASK_STATUS_PROCESS, ContractorTasks::TASK_STATUS_RETURNED], true)) {
                        $allowEdit = true;
                    }

                    $productParams = ContractorTaskSimilarProductParams::findAll(['deleted_at' => null, 'task_id' => $model->getTaskId()]);
                    $productParams = ArrayHelper::map($productParams, 'id', 'name');

                    $response = ['renderAjax' => $this->renderAjax('similar_product_list', [
                        'models' => $task->similarProducts, 'allowEdit' => $allowEdit,
                        'isMobile' => $isMobile, 'productParams' => $productParams, 'taskId' => $model->getTaskId()])];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    Yii::$app->response->data = $response;
                    return $response;
                }
            }
        }
        return false;
    }


    /**
     * @param int $id
     * @return array|false
     * @throws \Throwable
     */
    public function actionDeleteSimilarProduct(int $id)
    {
        if(Yii::$app->request->isAjax) {

            try {
                $model = ContractorTaskSimilarProducts::findOne($id);
                $model->delete();
                $success = true;
            } catch (\Exception $exception) {
                $success = false;
            }

            $response = ['success' => $success];
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $response;
            return $response;
        }
        return false;
    }
}
