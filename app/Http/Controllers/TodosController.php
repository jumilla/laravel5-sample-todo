<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Todo;

class TodosController extends Controller {

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct()
    {
        // beforeフィルタをインストールする
        $this->beforeFilter(
            '@existsFilter',
            ['on' => ['post', 'put']]
        );
    }

    /**
     * URLパラメータのidの存在をチェックする。
     *
     * @return void
     */
    public function existsFilter()
    {
        info(__METHOD__.' called.');

        // URLにパラメータ'id'が存在したら
        $id = Route::input('id');
        if ($id) {
            logger("todo id(${id}) checking...");

            // 指定のIDがtodosテーブルに存在しなかったら
            if (! Todo::withTrashed()->exists($id)) {
                logger('Nothing!');

                // Webブラウザに404 Not Foundを返す
                App::abort(404);
            }

            logger('Exists!');
        }
        else {
            logger('url was not contained $id.');
        }
    }

    /**
     * Todoリストページを表示する。
     *
     * @return void
     */
    public function index()
    {
        // 1. 3つのグループのデータを取得する

        // 1.1. 未完了リストを取得する
        {
            // クエリを作成する
            $query = Todo::query()->select('*')->where('status', '=', Todo::STATUS_INCOMPLETE)->orderBy('updated_at', 'desc');

            // クエリを実行し、結果を取得する
            // MEMO 1件の場合はfirst()を使う
            $incompleteTodos = $query->get();
        }

        // 1.2. 完了リストを取得する
        // MEMO シンタックスシュガーを使うとこのようにシンプルに書ける。
        $completedTodos = Todo::whereStatus(Todo::STATUS_COMPLETED)->orderBy('completed_at', 'desc')->get();

        // 1.3. 削除済みリストを取得する
        // MEMO ソフトデリートされたデータのフィルタリングはonlyTrashed()を使う。
        $trashedTodos = Todo::onlyTrashed()->get();

        // 2. ビューを生成する
        // MEMO 引数のための配列を生成するとき、compact()関数を使ってもいい。
        return view('pages.todos.index', [
            'incompleteTodos' => $incompleteTodos,
            'completedTodos' => $completedTodos,
            'trashedTodos' => $trashedTodos,
        ]);
    }

    /**
     * 新規Todoを追加する。
     *
	 * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request)
    {
        // バリデーションルールの定義
        $rules = [
            'title' => 'required|min:3|max:255',	// 'title'は必須で3文字以上255文字以内。
        ];

        // フォームの入力データを項目名を指定して取得する
        $input = $request->only(['title']);

        // バリデーターを生成する
        $validator = Validator::make($input, $rules);

        // バリデーションを行う
        if ($validator->fails()) {
            // バリデーションに失敗したら、バリデーションのエラー情報とフォームの入力値を追加してリストページにリダイレクトする。
            return redirect()->route('todos.index')->withErrors($validator)->withInput();
        }

        // Todoデータを作成する（SQL発行）
        $todo = Todo::create([
            'title' => $input['title'],
            'status' => Todo::STATUS_INCOMPLETE,
        ]);

        // リストページにリダイレクトする
        return redirect()->route('todos.index');
    }

    /**
     * Todoを更新する。
     *
	 * @param \Illuminate\Http\Request $request
     * @param integer $id TodoのID
     * @return void
     */
    public function update(Request $request, $id)
    {
        // Todoモデルを取得する
        $todo = Todo::find($id);

        // バリデーションルールの定義
        // MEMO 文字列でルールを'|'で区切ることで複数指定できる。
        // MEMO 配列でルールを複数指定することもできる。
        $rules = [
            'title' => 'required|min:3|max:255',
            'status' => ['required', 'numeric', 'min:1', 'max:2'],
            'dummy' => '',	// ルールを指定しないとオプション扱いにできる
        ];

        // 入力データを取得する
        // MEMO $inputの内容をログに出力して確認してみる。dummyキーはあるが値は空(null)になっている。
        $input = $request->only(array_keys($rules));
        logger(print_r($input, true));

        // バリデーションを実行する
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->route('todos.index')->withErrors($validator)->withInput();
        }

        // titleが指定されていたら
        if ($input['title'] !== null) {
            // titleカラムを更新する（SQL発行）
            $todo->update([
                'title' => $input['title'],
            ]);
        }

        // statusが指定されていたら
        if ($input['status'] !== null) {
            // statusとcompleted_atカラムを更新する（SQL発行）
            $todo->update([
                'status' => $input['status'],
                'completed_at' => $input['status'] == Todo::STATUS_COMPLETED ? Carbon::now() : null,
            ]);
        }

        // リストページにリダイレクトする
        return redirect()->route('todos.index');
    }

    /**
     * タイトルを更新する。
     *
	 * @param \Illuminate\Http\Request $request
     * @param integer $id TodoのID
     * @return void
     */
    public function ajaxUpdateTitle(Request $request, $id)
    {
        // Todoモデルを取得する
        $todo = Todo::find($id);

        // バリデーションルールの定義
        $rules = [
            'title' => 'required|min:3|max:255',
        ];

        // 入力データを取得する
        $input = $request->only(['title']);

        // バリデーションを実行する
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            // Ajaxレスポンスを返す
            return response()->json(['result' => 'NG', 'errors' => $validator->errors()], 400);
        }

        // titleカラムを更新する
        $todo->fill([
            'title' => $input['title'],
        ]);

        // データを更新する（SQL発行）
        $todo->save();

        // Ajaxレスポンスを返す
        return response()->json(['result' => 'OK'], 200);
    }

    /**
     * Todoを削除する。
     *
     * @param integer $id TodoのID
     * @return void
     */
    public function delete($id)
    {
        // Todoモデルを取得する
        $todo = Todo::find($id);

        // データを削除する（SQL発行）
        $todo->delete();

        // リストページにリダイレクトする
        return redirect()->route('todos.index');
    }

    /**
     * Todoを復元する。
     *
     * @param integer $id TodoのID
     * @return void
     */
    public function restore($id)
    {
        // 削除されたTodoモデルを取得する
        $todo = Todo::onlyTrashed()->find($id);

        // データを復元する（SQL発行）
        $todo->restore();

        // リストページにリダイレクトする
        return redirect()->route('todos.index');
    }

}
