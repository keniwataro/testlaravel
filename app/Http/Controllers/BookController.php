<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; //この2行を追加！
use Illuminate\Support\Facades\Auth;      //この2行を追加！

class BookController extends Controller
{
    /**
     * リソースの一覧を表示する
     */
    public function index()
    {
        $books = Book::where('user_id',Auth::user()->id)->orderBy('created_at', 'asc')->paginate(3);
        return view('books', [
            'books' => $books
        ]);
    }

    /**
     * 新しいリソースを作成するためのフォームを表示する
     */
    public function create()
    {
        //
    }

    /**
     * 新しく作成されたリソースをストレージに保存する
     */
    public function store(Request $request)
    {
        //バリデーション
        $validator = Validator::make($request->all(), [
            'item_name' => 'required|min:3|max:255',
            'item_number' => 'required | min:1 | max:3',
            'item_amount' => 'required | max:6',
            'published'   => 'required',
        ]);

        //バリデーション:エラー 
        if ($validator->fails()) {
            return redirect('/')
                ->withInput()
                ->withErrors($validator);
        }
        //以下に登録処理を記述（Eloquentモデル）

        // Eloquentモデル
        $books = new Book;
        $books->item_name   = $request->item_name;
        $books->user_id     = Auth::user()->id; //追加のコード
        $books->item_number = $request->item_number;
        $books->item_amount = $request->item_amount;
        $books->published   = $request->published;
        $books->save(); 
        return redirect('/');
    }

    /**
     * 指定されたリソースを表示する
     */
    public function show(Book $book)
    {
        //
    }

    /**
     * 指定されたリソースの編集用フォームを表示する
     */
    public function edit($book_id)
    {
        $books = Book::where('user_id',Auth::user()->id)->find($book_id);
        return view('booksedit', ['book' => $books]);
    }

    /**
     * 指定されたリソースをストレージで更新する
     */
    public function update(Request $request, Book $book)
    {
        //バリデーション
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'item_name' => 'required|min:3|max:255',
            'item_number' => 'required|min:1|max:3',
            'item_amount' => 'required|max:6',
            'published' => 'required',
        ]);
        //バリデーション:エラー
        if ($validator->fails()) {
            return redirect('/booksedit/'.$request->id)
                ->withInput()
                ->withErrors($validator);
        }
        
        //データ更新
        $books = Book::where('user_id',Auth::user()->id)->find($request->id);
        $books->item_name   = $request->item_name;
        $books->item_number = $request->item_number;
        $books->item_amount = $request->item_amount;
        $books->published   = $request->published;
        $books->save();
        return redirect('/');
    }

    /**
     * 指定されたリソースをストレージから削除する
     */
    public function destroy(Book $book)
    {
        $book->delete();       //追加
        return redirect('/');  //追加
    }
}
