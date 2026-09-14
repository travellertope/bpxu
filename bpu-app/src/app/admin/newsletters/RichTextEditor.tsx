'use client';

import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Underline from '@tiptap/extension-underline';
import Placeholder from '@tiptap/extension-placeholder';
import { useEffect } from 'react';

interface RichTextEditorProps {
    value: string;
    onChange: (html: string) => void;
    disabled?: boolean;
    placeholder?: string;
}

function ToolbarButton({
    onClick,
    active,
    disabled,
    label,
    children,
}: {
    onClick: () => void;
    active?: boolean;
    disabled?: boolean;
    label: string;
    children: React.ReactNode;
}) {
    return (
        <button
            type="button"
            title={label}
            aria-label={label}
            disabled={disabled}
            onMouseDown={e => e.preventDefault()}
            onClick={onClick}
            className="btn btn-sm"
            style={{
                padding: '4px 8px',
                minWidth: 30,
                background: active ? 'var(--purple)' : 'transparent',
                color: active ? '#fff' : undefined,
                border: '1px solid var(--border)',
            }}
        >
            {children}
        </button>
    );
}

export default function RichTextEditor({ value, onChange, disabled, placeholder }: RichTextEditorProps) {
    const editor = useEditor({
        immediatelyRender: false,
        editable: !disabled,
        extensions: [
            StarterKit.configure({ heading: { levels: [2, 3] } }),
            Underline,
            Link.configure({ openOnClick: false, autolink: true }),
            Image,
            Placeholder.configure({ placeholder: placeholder || 'Write your newsletter…' }),
        ],
        content: value || '',
        editorProps: {
            attributes: {
                class: 'rte-content',
                style: 'min-height:260px;padding:12px;outline:none;font-size:0.95rem;line-height:1.6;',
            },
        },
        onUpdate: ({ editor }) => onChange(editor.getHTML()),
    });

    useEffect(() => {
        if (!editor) return;
        editor.setEditable(!disabled);
    }, [disabled, editor]);

    useEffect(() => {
        if (!editor) return;
        if (value !== editor.getHTML()) {
            editor.commands.setContent(value || '', false);
        }
        // Only re-sync when the external value changes (e.g. switching campaigns).
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [value]);

    if (!editor) return null;

    function setLink() {
        const previousUrl = editor!.getAttributes('link').href as string | undefined;
        const url = window.prompt('Link URL', previousUrl || 'https://');
        if (url === null) return;
        if (url === '') {
            editor!.chain().focus().extendMarkRange('link').unsetLink().run();
            return;
        }
        editor!.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    }

    function addImage() {
        const url = window.prompt('Image URL');
        if (!url) return;
        editor!.chain().focus().setImage({ src: url }).run();
    }

    return (
        <div className="field-input" style={{ padding: 0, overflow: 'hidden' }}>
            {!disabled && (
                <div
                    className="flex flex-wrap gap-1"
                    style={{ padding: 6, borderBottom: '1px solid var(--border)', background: 'var(--bg-2, rgba(0,0,0,0.02))' }}
                >
                    <ToolbarButton label="Bold" active={editor.isActive('bold')} onClick={() => editor.chain().focus().toggleBold().run()}>
                        <strong>B</strong>
                    </ToolbarButton>
                    <ToolbarButton label="Italic" active={editor.isActive('italic')} onClick={() => editor.chain().focus().toggleItalic().run()}>
                        <em>I</em>
                    </ToolbarButton>
                    <ToolbarButton label="Underline" active={editor.isActive('underline')} onClick={() => editor.chain().focus().toggleUnderline().run()}>
                        <span style={{ textDecoration: 'underline' }}>U</span>
                    </ToolbarButton>
                    <ToolbarButton label="Heading" active={editor.isActive('heading', { level: 2 })} onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()}>
                        H2
                    </ToolbarButton>
                    <ToolbarButton label="Bullet list" active={editor.isActive('bulletList')} onClick={() => editor.chain().focus().toggleBulletList().run()}>
                        •—
                    </ToolbarButton>
                    <ToolbarButton label="Numbered list" active={editor.isActive('orderedList')} onClick={() => editor.chain().focus().toggleOrderedList().run()}>
                        1.
                    </ToolbarButton>
                    <ToolbarButton label="Quote" active={editor.isActive('blockquote')} onClick={() => editor.chain().focus().toggleBlockquote().run()}>
                        &ldquo;
                    </ToolbarButton>
                    <ToolbarButton label="Link" active={editor.isActive('link')} onClick={setLink}>
                        🔗
                    </ToolbarButton>
                    <ToolbarButton label="Image" onClick={addImage}>
                        🖼
                    </ToolbarButton>
                    <ToolbarButton label="Divider" onClick={() => editor.chain().focus().setHorizontalRule().run()}>
                        ―
                    </ToolbarButton>
                    <ToolbarButton label="Clear formatting" onClick={() => editor.chain().focus().clearNodes().unsetAllMarks().run()}>
                        ⨯
                    </ToolbarButton>
                </div>
            )}
            <EditorContent editor={editor} />
        </div>
    );
}
