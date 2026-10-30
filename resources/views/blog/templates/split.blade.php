<article class="post post--split" itemscope itemtype="https://schema.org/Article">
    <div class="post-split">
        <div class="post-split-media">
            <img class="post-cover" src="{{ $post->cover_url }}" alt="{{ $post->title }}" width="1200" height="630" itemprop="image">
        </div>
        <div class="post-split-text">
@include('blog.templates._head')
@include('blog.templates._toc', ['tocClass' => ' post-toc--flat'])
@include('blog.templates._body')
        </div>
    </div>
</article>
