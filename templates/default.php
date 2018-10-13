<section class="gallery is-loading">
    <div class="wrapper"> 
        <?php foreach($items as $src => $item): ?>    
            <img 
                src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" 
                data-src="<?= $imagePath . '/' . $src; ?>"
                data-src-gallery="<?= $imagePath . '/' . $src; ?>" 
                <?= FolderGallery::renderSizesAttributes($galleryPath . '/' . $src); ?>   
                <?= FolderGallery::renderAttributes($item); ?>   
            >
        <?php endforeach; ?>
    </div>
</section>
        