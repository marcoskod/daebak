<article class="post post--hero-overlay" itemscope itemtype="https://schema.org/Article">
    <div class="post-hero">
        <img class="post-hero-img" src="{{ $post->cover_url }}" alt="" width="1200" height="630" itemprop="image">
@include('blog.templates._head', ['headClass' => ' post-head--overlay'])
    </div>
    <div class="post-layout">
@include('blog.templates._toc')
@include('blog.templates._body')
    </div>
</article>
