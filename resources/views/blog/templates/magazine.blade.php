<article class="post post--magazine" itemscope itemtype="https://schema.org/Article">
@include('blog.templates._cover', ['coverClass' => ' post-cover--wide'])
@include('blog.templates._head', ['headClass' => ' post-head--center'])
    <div class="post-layout">
@include('blog.templates._toc')
@include('blog.templates._body', ['bodyClass' => ' post-body--dropcap'])
    </div>
</article>
