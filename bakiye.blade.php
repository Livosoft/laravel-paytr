           <form action="{{route('odeme_ekranix')}}" method="POST">
                @csrf

                <div class="card rounded-0 border-0 card2" id="paypage">
                    <div class="form-card">

                        <div class="radio-group">
                            <div class='radio' data-value="credit"><img src="https://i.imgur.com/28akQFX.jpg" width="200px" height="60px"></div>
                           <br>
                           <label class="pay">Miktar</label> <input type="text" name="miktar" placeholder="Yüklenecek Miktar">

                        </div>

                    </div>
                </div>

                <div class="clearfix"></div>
                <hr class="line-separator">
                <button type="submit" class="button big dark">Şimdi <span class="primary">Yükle!</span></button>
            </form>
