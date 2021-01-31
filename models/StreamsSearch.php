<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Streams;

/**
 * StreamsSearch represents the model behind the search form of `app\models\Streams`.
 */
class StreamsSearch extends Streams
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'platform_id', 'priority', 'domain_id'], 'integer'],
            [['name'], 'string'],
            [['channel'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Streams::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'platform_id' => $this->platform_id,
            'priority' => $this->priority,
            'domain_id' => $this->domain_id,
        ]);

        $query->andFilterWhere(['like', 'channel', $this->channel]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
