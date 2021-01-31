<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Video;

/**
 * VideoSearch represents the model behind the search form of `app\models\Video`.
 */
class VideoSearch extends Video
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'domain_id', 'platform_id'], 'integer'],
            [['channel_id'], 'safe'],
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
        $query = Video::find();

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
            'domain_id' => $this->domain_id,
            'platform_id' => $this->platform_id,
        ]);

        $query->andFilterWhere(['like', 'channel_id', $this->channel_id]);

        return $dataProvider;
    }
}
