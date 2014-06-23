<?php
/**
 * Created by PhpStorm.
 * User: mark
 * Date: 6/19/14
 * Time: 12:59 PM
 */

namespace app\webvimark\components;


use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class BaseController extends Controller
{
	/**
	 * @var ActiveRecord
	 */
	public $modelClass;

	/**
	 * @var ActiveRecord
	 */
	public $modelSearchClass;


	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

	/**
	 * Lists all Excursion models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$searchModel  = $this->modelSearchClass ? new $this->modelSearchClass : null;

		if ( $searchModel )
		{
			$dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
		}
		else
		{
			$modelClass = $this->modelClass;
			$dataProvider = new ActiveDataProvider([
				'query' => $modelClass::find(),
			]);
		}

		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'searchModel'  => $searchModel,
		]);
	}

	/**
	 * Displays a single Excursion model.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

	/**
	 * Creates a new Excursion model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate()
	{
		$model = new $this->modelClass;

		if ( $model->load(Yii::$app->request->post()) && $model->save() )
		{
			return $this->redirect([
				'view',
				'id' => $model->id
			]);
		}
		else
		{
			return $this->render('create', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Updates an existing Excursion model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionUpdate($id)
	{
		$model = $this->findModel($id);

		if ( $model->load(Yii::$app->request->post()) AND $model->save())
		{
			return $this->redirect([
				'view',
				'id' => $model->id
			]);
		}
		else
		{
			return $this->render('update', [
				'model' => $model,
			]);
		}
	}

	/**
	 * Deletes an existing Excursion model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 *
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}


	/**
	 * @param string $attribute
	 * @param int $id
	 */
	public function actionToggleAttribute($attribute, $id)
	{
		$model = $this->findModel($id);
		$model->{$attribute} = ($model->{$attribute} == 1) ? 0 : 1;
		$model->save(false);
	}


	/**
	 * Activate all selected grid items
	 */
	public function actionBulkActivate()
	{
		if ( \Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			$modelClass::updateAll(
				['active'=>1],
				['id'=>\Yii::$app->request->post('selection', [])]
			);
		}
	}

	/**
	 * Deactivate all selected grid items
	 */
	public function actionBulkDeactivate()
	{
		if ( \Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			$modelClass::updateAll(
				['active'=>0],
				['id'=>\Yii::$app->request->post('selection', [])]
			);
		}
	}


	/**
	 * Deactivate all selected grid items
	 */
	public function actionBulkDelete()
	{
		if ( \Yii::$app->request->post('selection') )
		{
			$modelClass = $this->modelClass;

			$modelClass::deleteAll(
				['id'=>\Yii::$app->request->post('selection', [])]
			);
		}
	}


	/**
	 * Sorting items in grid
	 */
	public function actionGridSort()
	{
		if ( \Yii::$app->request->post('sorter') )
		{
			$sortArray = \Yii::$app->request->post('sorter',[]);
			echo "<pre>";
			var_dump($sortArray);
			echo "</pre>";
			die();

			$modelClass = $this->modelClass;

			$models = $modelClass::findAll(array_keys($sortArray));

			foreach ($models as $model)
			{
				$model->sorter = $sortArray[$model->id][0];
				$model->save(false);
			}

		}
	}

	/**
	 * Finds the Excursion model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param integer $id
	 *
	 * @return ActiveRecord the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		$modelClass = $this->modelClass;

		if ( ($model = $modelClass::findOne($id)) !== null )
		{
			return $model;
		}
		else
		{
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}

} 