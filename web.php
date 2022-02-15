Route::post('/odeme-ekrani','User\UserController@odeme_ekraniq')->name('odeme_ekranix'); //paytr ödeme ekranının açılacağı sayfa
Route::get('/odeme-basarili','User\UserController@odeme_basarili')->name('odeme_basarili'); //2 adet route oluşturuyorum biri get diğeri post
Route::post('/odeme-basarili','User\UserController@odeme_basarili')->name('odeme_basarili'); // post değerli route
Route::get('/odeme-basarisiz','User\UserController@odeme_basarisiz')->name('odeme_basarisiz'); // ödeme başarısız olduğunda dönecek sayfa
Route::get('/basarili-odeme','User\UserController@basariliodeme')->name('odeme_okey'); // ödeme tamamlandıyı gösterdigim sayfa
